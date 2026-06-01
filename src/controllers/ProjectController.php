<?php
// src/controllers/ProjectController.php

function indexAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }


    $user_id = authUser()['id'];

    $stmt = $pdo->prepare('
        SELECT p.id, p.proposal_id, p.code, p.start_date, p.status as status_name, p.closure_requested,
               pr.title, u.name as researcher_name, "project" as item_type,
               COALESCE((SELECT SUM(percentage_complete) FROM progress_reports WHERE project_id = p.id), 0) as total_progress,
               (SELECT status FROM final_reports WHERE project_id = p.id LIMIT 1) as final_report_status
        FROM projects p
        LEFT JOIN proposals pr ON p.proposal_id = pr.id
        LEFT JOIN users u ON pr.user_id = u.id
        WHERE pr.user_id = ?
    ');
    $stmt->execute([$user_id]);
    $projects = $stmt->fetchAll();

    // Also fetch standalone proposals that aren't projects yet
    $stmtProp = $pdo->prepare('
        SELECT pr.id as proposal_id, pr.title, pr.status as status_name, pr.created_at as start_date, 
               "proposal" as item_type, 0 as total_progress
        FROM proposals pr
        LEFT JOIN projects p ON pr.id = p.proposal_id
        WHERE pr.user_id = ? AND p.id IS NULL
    ');
    $stmtProp->execute([$user_id]);
    $pending_proposals = $stmtProp->fetchAll();
    
    // Merge datasets visually for the view
    $all_items = array_merge($projects, $pending_proposals);

    // Sort combined array by start date / created date descending
    usort($all_items, function($a, $b) {
        return strtotime($b['start_date']) - strtotime($a['start_date']);
    });

    require __DIR__ . '/../../views/projects/index.php';
}

function allAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $stmt = $pdo->prepare('
        SELECT p.id, p.proposal_id, p.code, p.start_date, p.status as status_name, p.closure_requested,
               pr.title, u.name as researcher_name, "project" as item_type,
               COALESCE((SELECT SUM(percentage_complete) FROM progress_reports WHERE project_id = p.id), 0) as total_progress,
               (SELECT status FROM final_reports WHERE project_id = p.id LIMIT 1) as final_report_status
        FROM projects p
        LEFT JOIN proposals pr ON p.proposal_id = pr.id
        LEFT JOIN users u ON pr.user_id = u.id
        ORDER BY p.start_date DESC
    ');
    $stmt->execute();
    $projects = $stmt->fetchAll();

    require __DIR__ . '/../../views/projects/all.php';
}

function showAction($pdo, $id) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    // Allow full access to admins, restrict users to their own projects
    $user_id = authUser()['id'];
    $isAdmin = hasRole('admin');

    // Get project
    $stmt = $pdo->prepare('
        SELECT p.*, p.status as status, 
               pr.budget_total, pr.title, f.name as funding_source_name, u.name as leader_name,
               pr.user_id as user_id,
               COALESCE((SELECT SUM(amount) FROM proposal_budget_items WHERE proposal_id = p.proposal_id), 0) as budget_used,
               COALESCE((SELECT SUM(percentage_complete) FROM progress_reports WHERE project_id = p.id), 0) as total_progress,
               (SELECT status FROM final_reports WHERE project_id = p.id LIMIT 1) as final_report_status
        FROM projects p
        LEFT JOIN proposals pr ON p.proposal_id = pr.id
        LEFT JOIN funding_sources f ON pr.funding_source_id = f.id
        LEFT JOIN users u ON pr.user_id = u.id
        WHERE p.id = ?
    ');
    $stmt->execute([$id]);
    $project = $stmt->fetch();

    if (!$project) {
        die("Project not found");
    }

    if (!$isAdmin && $project['user_id'] != $user_id) {
        die("Access denied: You do not have permission to view this project.");
    }

    // Get related data
    $stmt = $pdo->prepare('SELECT * FROM progress_reports WHERE project_id = ? ORDER BY created_at DESC');
    $stmt->execute([$id]);
    $progressReports = $stmt->fetchAll();

    // Fetch from the correct tables after schema update
    $stmt = $pdo->prepare('SELECT * FROM publications WHERE project_id = ? ORDER BY publish_year DESC, created_at DESC');
    $stmt->execute([$id]);
    $outputs = $stmt->fetchAll();

    $stmt = $pdo->prepare('SELECT * FROM ip_creations WHERE project_id = ? ORDER BY created_at DESC');
    $stmt->execute([$id]);
    $ipAssets = $stmt->fetchAll();

    require __DIR__ . '/../../views/projects/show.php';
}

function requestClosureAction($pdo) {
    if (!isLoggedIn()) redirect('/login');
    $id = $_GET['id'] ?? null;
    if (!$id) die("Project ID is required");

    $user_id = authUser()['id'];
    
    // Check ownership and progress
    $stmt = $pdo->prepare('
        SELECT p.*, pr.user_id,
               (SELECT SUM(percentage_complete) FROM progress_reports WHERE project_id = p.id) as total_progress,
               (SELECT status FROM final_reports WHERE project_id = p.id LIMIT 1) as final_report_status
        FROM projects p 
        JOIN proposals pr ON p.proposal_id = pr.id 
        WHERE p.id = ?
    ');
    $stmt->execute([$id]);
    $project = $stmt->fetch();

    if (!$project || $project['user_id'] != $user_id) {
        die("Unauthorized");
    }

    if (($project['total_progress'] ?? 0) < 100) {
        $_SESSION['error_msg'] = "รายงานยังไม่สมบูรณ์ ไม่สามารถขอเสนอปิดโครงการได้ (ความก้าวหน้าปัจจุบัน: " . ($project['total_progress'] ?: 0) . "%)";
        redirect('/projects');
    }

    if (!in_array($project['final_report_status'] ?? '', ['submitted', 'approved'])) {
        $_SESSION['error_msg'] = "กรุณาส่งรายงานฉบับสมบูรณ์ (Final Report) ก่อนขอเสนอปิดโครงการ";
        redirect('/projects');
    }

    $stmt = $pdo->prepare('UPDATE projects SET closure_requested = 1 WHERE id = ?');
    $stmt->execute([$id]);

    // Notify Admins
    $adminStmt = $pdo->query("SELECT u.id FROM users u JOIN model_has_roles mhr ON u.id = mhr.model_id JOIN roles r ON mhr.role_id = r.id WHERE r.name = 'admin' OR r.name = 'System Administrator'");
    $admins = $adminStmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($admins as $adminId) {
        addNotification($pdo, $adminId, "คำขอปิดโครงการ", "โครงการ " . $project['code'] . " ได้ส่งคำขอปิดโครงการ");
    }

    $_SESSION['alert_msg'] = "ส่งคำขอปิดโครงการเรียบร้อยแล้ว";
    redirect('/projects');
}

function approveClosureAction($pdo) {
    if (!isLoggedIn()) redirect('/login');
    requireRole('admin');
    
    $id = $_GET['id'] ?? null;
    if (!$id) die("Project ID is required");

    $stmt = $pdo->prepare('SELECT p.*, pr.user_id, pr.title, pr.budget_total FROM projects p JOIN proposals pr ON p.proposal_id = pr.id WHERE p.id = ?');
    $stmt->execute([$id]);
    $project = $stmt->fetch();

    if (!$project) die("Project not found");

    // Update both project and proposal status to "closed"
    $stmt = $pdo->prepare('UPDATE projects SET status = "closed", closure_requested = 0 WHERE id = ?');
    $stmt->execute([$id]);

    $stmtProposal = $pdo->prepare('UPDATE proposals SET status = "closed" WHERE id = ?');
    $stmtProposal->execute([$project['proposal_id']]);

    // Notify Owner
    addNotification($pdo, $project['user_id'], "โครงการถูกปิดแล้ว", "โครงการ " . $project['title'] . " ได้รับการอนุมัติปิดโครงการแล้ว");

    $_SESSION['alert_msg'] = "ปิดโครงการเรียบร้อยแล้ว ข้อมูลได้ถูกบันทึกลงสถิติแล้ว";
    redirect('/projects/' . $id);
}


