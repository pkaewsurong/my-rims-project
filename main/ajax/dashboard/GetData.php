<?php
// main/ajax/dashboard/GetData.php - AJAX endpoint for performance dashboard
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$year = (int)($_POST['year'] ?? (date('Y') + 543));
$gregorianYear = $year - 543;
$researcherId = isset($_POST['researcher_id']) && $_POST['researcher_id'] !== '' ? (int)$_POST['researcher_id'] : null;

// Filter condition
$researcherConditionProposal = $researcherId ? " AND pr.user_id = " . $researcherId : "";
$researcherConditionProposalP = $researcherId ? " AND p.user_id = " . $researcherId : "";

// 1. Snapshot Data
if ($researcherId) {
    $total_researchers = 1;
} else {
    $stmtResearchers = $pdo->query("
        SELECT COUNT(DISTINCT u.id) 
        FROM users u
        JOIN model_has_roles mhr ON u.id = mhr.model_id
        JOIN roles r ON mhr.role_id = r.id
        WHERE r.name IN ('Researcher', 'Research Admin')
    ");
    $total_researchers = $stmtResearchers->fetchColumn();
}

$stmtCompleted = $pdo->query("
    SELECT 
        COUNT(*) as total_completed,
        SUM(CASE WHEN pr.budget_total > 1000000 THEN 1 ELSE 0 END) as high_impact_completed
    FROM projects p
    JOIN proposals pr ON p.proposal_id = pr.id
    WHERE p.status = 'closed' 
    AND YEAR(pr.created_at) = " . (int)$gregorianYear . "
    " . $researcherConditionProposal . "
");
$completedData = $stmtCompleted->fetch(PDO::FETCH_ASSOC);

$stmtGrants = $pdo->query("
    SELECT SUM(f.amount) as external_grants_value
    FROM proposal_funding_sources f
    JOIN proposals p ON f.proposal_id = p.id
    WHERE (p.status = 'closed' OR p.status = 'approved') 
    AND (f.type = 'External' OR f.name LIKE '%ภายนอก%') 
    AND YEAR(p.created_at) = " . (int)$gregorianYear . "
    " . $researcherConditionProposalP . "
");
$grantsData = $stmtGrants->fetch(PDO::FETCH_ASSOC);

$snapshot = [
    'total_researchers' => $total_researchers,
    'external_grants_value' => $grantsData['external_grants_value'] ?: 0,
    'high_impact_completed' => $completedData['high_impact_completed'] ?: 0,
    'total_completed' => $completedData['total_completed'] ?: 0,
];

// 2. Project Status
$stmt = $pdo->query("
    SELECT COALESCE(pj.status, p.status) as final_status, COUNT(*) as count 
    FROM proposals p
    LEFT JOIN projects pj ON p.id = pj.proposal_id
    WHERE YEAR(p.created_at) = " . (int)$gregorianYear . "
    " . $researcherConditionProposalP . "
    GROUP BY final_status
");
$rawData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$projectStatusData = [
    'closed' => (int)($rawData['closed'] ?? 0),
    'approved' => (int)(($rawData['approved'] ?? 0) + ($rawData['ongoing'] ?? 0)),
    'under_review' => (int)($rawData['under_review'] ?? 0),
    'submitted' => (int)($rawData['submitted'] ?? 0),
    'draft' => (int)($rawData['draft'] ?? 0)
];

// 3. Top Researchers
$topResearcherFilter = $researcherId ? " AND u.id = " . $researcherId : "";
$stmt = $pdo->query("
    SELECT u.name, 
           (SELECT COUNT(*) FROM projects j JOIN proposals pr ON j.proposal_id = pr.id WHERE pr.user_id = u.id AND j.status = 'closed' AND YEAR(pr.created_at) = " . (int)$gregorianYear . ") as completed_count,
           (SELECT COALESCE(SUM(f.amount), 0) FROM proposal_funding_sources f JOIN proposals pr ON f.proposal_id = pr.id WHERE pr.user_id = u.id AND (pr.status = 'approved' OR pr.status = 'closed') AND YEAR(pr.created_at) = " . (int)$gregorianYear . ") as total_grant,
           COALESCE(m.h_index, 0) as h_index
    FROM users u 
    JOIN model_has_roles mhr ON u.id = mhr.model_id
    JOIN roles r ON mhr.role_id = r.id
    LEFT JOIN metric_snapshots m ON u.id = m.user_id AND m.fiscal_year = " . (int)$gregorianYear . "
    WHERE r.name IN ('Researcher', 'Research Admin')
    " . $topResearcherFilter . "
    ORDER BY completed_count DESC, total_grant DESC
    LIMIT 5
");
$topResearchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Trends
$stmtTrendCompleted = $pdo->query("
    SELECT YEAR(pr.created_at) as year, COUNT(*) as count 
    FROM projects p
    JOIN proposals pr ON p.proposal_id = pr.id
    WHERE p.status = 'closed'
    " . $researcherConditionProposal . "
    GROUP BY YEAR(pr.created_at)
    ORDER BY year ASC
");
$rawCompleted = $stmtTrendCompleted->fetchAll(PDO::FETCH_KEY_PAIR);

$stmtTrendGrants = $pdo->query("
    SELECT YEAR(p.created_at) as year, SUM(f.amount) as total_grant
    FROM proposals p
    JOIN proposal_funding_sources f ON p.id = f.proposal_id
    WHERE (p.status = 'approved' OR p.status = 'closed')
    " . $researcherConditionProposalP . "
    GROUP BY YEAR(p.created_at)
    ORDER BY year ASC
");
$rawGrants = $stmtTrendGrants->fetchAll(PDO::FETCH_KEY_PAIR);

$trends = [];
for ($y = $gregorianYear - 4; $y <= $gregorianYear; $y++) {
    $trends[] = [
        'year' => $y + 543,
        'completed' => (int)($rawCompleted[$y] ?? 0),
        'grants' => isset($rawGrants[$y]) ? round((float)$rawGrants[$y] / 1000000, 2) : 0
    ];
}

// 5. Funding Source Proportion
$stmtFundingProp = $pdo->query("
    SELECT 
        CASE 
            WHEN f.type = 'External' OR f.name LIKE '%ภายนอก%' THEN 'External'
            ELSE 'Internal'
        END as categorical_type,
        SUM(f.amount) as total
    FROM proposal_funding_sources f
    JOIN proposals p ON f.proposal_id = p.id
    WHERE (p.status = 'approved' OR p.status = 'closed') AND YEAR(p.created_at) = " . (int)$gregorianYear . "
    " . $researcherConditionProposalP . "
    GROUP BY categorical_type
");
$fundingData = $stmtFundingProp->fetchAll(PDO::FETCH_KEY_PAIR);

$fundingProportion = [
    'Internal' => (float)($fundingData['Internal'] ?? 0),
    'External' => (float)($fundingData['External'] ?? 0)
];

header('Content-Type: application/json');
echo json_encode([
    'snapshot'          => $snapshot,
    'projectStatusData' => $projectStatusData,
    'topResearchers'    => $topResearchers,
    'trends'            => $trends,
    'fundingProportion' => $fundingProportion
]);
