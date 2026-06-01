<?php
// src/controllers/DashboardController.php

function researchDashboardAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $year = $_GET['year'] ?? date('Y') + 543;
    $gregorianYear = $year - 543;
    $researcherId = isset($_GET['researcher_id']) && $_GET['researcher_id'] !== '' ? (int)$_GET['researcher_id'] : null;

    // Fetch all researchers for the filter dropdown
    $stmtAllResearchers = $pdo->query("
        SELECT u.id, u.name
        FROM users u
        JOIN model_has_roles mhr ON u.id = mhr.model_id
        JOIN roles r ON mhr.role_id = r.id
        WHERE r.name IN ('Researcher', 'Research Admin')
        ORDER BY u.name ASC
    ");
    $allResearchers = $stmtAllResearchers->fetchAll(PDO::FETCH_ASSOC);

    // Build researcher filter condition for proposals
    $researcherConditionProposal = $researcherId ? " AND pr.user_id = " . $researcherId : "";
    $researcherConditionProposalP = $researcherId ? " AND p.user_id = " . $researcherId : "";

    // 1. Snapshot Data (Real calculated metrics for the given year)
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
        'total_citations' => 0, 
        'external_grants_value' => $grantsData['external_grants_value'] ?: 0,
        'high_impact_completed' => $completedData['high_impact_completed'] ?: 0,
        'total_completed' => $completedData['total_completed'] ?: 0,
        'last_calculated_at' => date('Y-m-d H:i:s')
    ];

    // 2. Fetch Projects Summary for Charts (Real aggregated data)
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
        'approved' => ($rawData['approved'] ?? 0) + ($rawData['ongoing'] ?? 0),
        'closed' => $rawData['closed'] ?? 0,
        'under_review' => $rawData['under_review'] ?? 0,
        'submitted' => $rawData['submitted'] ?? 0,
        'draft' => $rawData['draft'] ?? 0
    ];

    // 3. Fetch Top Researchers (Based on completed projects and grants)
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

    // 4. Trend Data for Charts (Completed projects and funding)
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
            'completed' => $rawCompleted[$y] ?? 0,
            'grants' => isset($rawGrants[$y]) ? $rawGrants[$y] / 1000000 : 0 // Convert to millions
        ];
    }

    // 5. Funding Source Proportion (Real data)
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
        'Internal' => $fundingData['Internal'] ?? 0,
        'External' => $fundingData['External'] ?? 0
    ];

    require __DIR__ . '/../../views/dashboard/research.php';
}

function researchersListAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $year = $_GET['year'] ?? date('Y') + 543;
    $gregorianYear = $year - 543;

    // Fetch All Researchers ranked by completed_count and total_grant
    $stmt = $pdo->query("
        SELECT u.name, 
               (SELECT COUNT(*) FROM projects j JOIN proposals pr ON j.proposal_id = pr.id WHERE pr.user_id = u.id AND j.status = 'closed' AND YEAR(pr.created_at) = " . (int)$gregorianYear . ") as completed_count,
               (SELECT SUM(f.amount) FROM proposal_funding_sources f JOIN proposals pr ON f.proposal_id = pr.id WHERE pr.user_id = u.id AND (pr.status = 'approved' OR pr.status = 'closed') AND YEAR(pr.created_at) = " . (int)$gregorianYear . ") as total_grant,
               COALESCE(m.h_index, 0) as h_index
        FROM users u 
        JOIN model_has_roles mhr ON u.id = mhr.model_id
        JOIN roles r ON mhr.role_id = r.id
        LEFT JOIN metric_snapshots m ON u.id = m.user_id AND m.fiscal_year = " . (int)$gregorianYear . "
        WHERE r.name IN ('Researcher', 'Research Admin')
        ORDER BY completed_count DESC, total_grant DESC
    ");
    $researchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require __DIR__ . '/../../views/dashboard/researchers_list.php';
}
