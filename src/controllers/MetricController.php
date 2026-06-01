<?php
// src/controllers/MetricController.php

function indexAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    
    // Check role, typically research admins or executives need this
    if (!hasAnyRole(['research_admin', 'executive', 'researcher'])) {
        die("Unauthorized");
    }

    $year = $_GET['year'] ?? date('Y') + 543; // Default to current Thai year
    $faculty_id = $_GET['faculty_id'] ?? null;
    $user_id = $_GET['user_id'] ?? null;

    // Build conditions
    $conditions = [];
    $params = [];

    // Mock calculations based on actual data
    // In a real scenario, this would involve complex joins and aggregation.
    // For this demonstration, we'll try to fetch some real stats to show a dynamic dashboard.

    $user_query = "";
    if ($user_id) {
        $user_query = " AND user_id = ?";
        $params[] = $user_id;
    }

    // 1. External Grants
    $stmt = $pdo->prepare("SELECT SUM(budget_total) as external_grants FROM proposals WHERE funding_source_external IS NOT NULL AND status = 'approved' " . $user_query);
    $stmt->execute($params);
    $grantData = $stmt->fetch(PDO::FETCH_ASSOC);
    $externalGrants = $grantData['external_grants'] ?? 0;

    // 2. Total Publications & Q1/Q2
    // Publications linked to projects, which link to proposals, which link to users
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_pubs,
            SUM(CASE WHEN p.indexing_database IN ('Scopus', 'WoS') THEN 1 ELSE 0 END) as q1_q2_pubs
        FROM publications p
        JOIN projects j ON p.project_id = j.id
        JOIN proposals pr ON j.proposal_id = pr.id
        WHERE 1=1 " . str_replace('user_id', 'pr.user_id', $user_query) . "
    ");
    $stmt->execute($params);
    $pubData = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalPubs = $pubData['total_pubs'] ?? 0;
    $q1q2Pubs = $pubData['q1_q2_pubs'] ?? 0;

    // 3. IP Assets
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_ip
        FROM ip_assets ip
        JOIN projects j ON ip.project_id = j.id
        JOIN proposals pr ON j.proposal_id = pr.id
        WHERE 1=1 " . str_replace('user_id', 'pr.user_id', $user_query) . "
    ");
    $stmt->execute($params);
    $ipData = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalIps = $ipData['total_ip'] ?? 0;

    // Fake H-Index and Citation Count for Demo (Since we don't store individual citations per pub in basic schema)
    $hIndex = $user_id ? rand(5, 15) : rand(15, 45); // Higher for faculty
    $citationCount = $user_id ? rand(20, 100) : rand(100, 500);

    // Fetch publications list for the bottom table
    $stmt = $pdo->prepare("
        SELECT p.title_th as title, p.journal_name, p.publish_year as publication_year, p.indexing_database as indexing, j.code as project_code 
        FROM publications p
        JOIN projects j ON p.project_id = j.id
        JOIN proposals pr ON j.proposal_id = pr.id
        WHERE 1=1 " . str_replace('user_id', 'pr.user_id', $user_query) . "
        ORDER BY p.publish_year DESC LIMIT 10
    ");
    $stmt->execute($params);
    $publicationsList = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Fetch users for dropdown
    $usersStmt = $pdo->query("SELECT id, name FROM users WHERE id IN (SELECT model_id FROM model_has_roles WHERE role_id = (SELECT id FROM roles WHERE name = 'Researcher'))");
    $researchers = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

    require __DIR__ . '/../../views/metrics/index.php';
}

// Action to trigger a recalculation (Sync)
function syncAction($pdo) {
    if (!isLoggedIn() || !hasRole('research_admin')) {
        die("Unauthorized");
    }

    // Attempt to calculate and log a snapshot
    $user_id = $_POST['user_id'] ?? null;
    $fiscal_year = $_POST['year'] ?? date('Y');

    // MOCK SYNC PROCESS -> Insert snapshot
    try {
        $stmt = $pdo->prepare("
            INSERT INTO metric_snapshots (user_id, fiscal_year, h_index, total_citations, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $user_id !== 'all' ? $user_id : null,
            $fiscal_year,
            rand(5, 20),
            rand(50, 200)
        ]);
        
        $_SESSION['flash_success'] = "ซิงค์ข้อมูลชี้วัดวิจัยสำเร็จแล้ว (Sync completed)";
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "เกิดข้อผิดพลาดในการซิงค์: " . $e->getMessage();
    }
    
    redirect('/metrics');
}
