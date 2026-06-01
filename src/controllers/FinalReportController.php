<?php
// src/controllers/FinalReportController.php

function createAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $project_id = $_GET['id'] ?? null;
    if (!$project_id) {
        die("Project ID is required");
    }

    // Fetch project details
    $stmt = $pdo->prepare('
        SELECT p.*, pr.title, pr.user_id, pr.budget_total,
               COALESCE((SELECT SUM(amount) FROM proposal_budget_items WHERE proposal_id = p.proposal_id), 0) as budget_used
        FROM projects p 
        JOIN proposals pr ON p.proposal_id = pr.id 
        WHERE p.id = ?
    ');
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();

    if (!$project) {
        die("Project not found");
    }

    // Verify ownership
    if ($project['user_id'] != authUser()['id']) {
        die("Unauthorized");
    }
    
    // Calculate Budget Usage
    $budget_used = $project['budget_used'] ?? 0;
    $budget_total = $project['budget_total'] ?? 0;
    $budget_percentage = $budget_total > 0 ? round(($budget_used / $budget_total) * 100) : 0;
    $budget_remaining = max(0, $budget_total - $budget_used);
    
    // Check if report already submitted
    $stmtCheck = $pdo->prepare('SELECT id, status FROM final_reports WHERE project_id = ?');
    $stmtCheck->execute([$project_id]);
    $existingReport = $stmtCheck->fetch();

    if ($existingReport && $existingReport['status'] !== 'draft') {
        $_SESSION['alert_msg'] = "โครงการนี้ได้ส่งรายงานฉบับสมบูรณ์ไปแล้ว";
        redirect('/projects/' . $project_id);
    }

    require __DIR__ . '/../../views/reports/final_report.php';
}

function storeAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project_id = $_POST['project_id'] ?? null;
        if (!$project_id) die("Project ID is required");

        $submission_date = $_POST['submission_date'] ?? date('Y-m-d');
        $executive_summary = $_POST['executive_summary'] ?? '';
        $utilization_impact = $_POST['utilization_impact'] ?? '';
        $curriculum_suggestions = $_POST['curriculum_suggestions'] ?? '';
        $faculty_suggestions = $_POST['faculty_suggestions'] ?? '';
        
        $checklist_report_sent = isset($_POST['checklist_report_sent']) ? 1 : 0;
        $checklist_budget_cleared = isset($_POST['checklist_budget_cleared']) ? 1 : 0;
        $checklist_outputs_registered = isset($_POST['checklist_outputs_registered']) ? 1 : 0;
        $checklist_project_closed = isset($_POST['checklist_project_closed']) ? 1 : 0;
        
        $status = $_POST['action_type'] === 'draft' ? 'draft' : 'submitted';

        // Handle File Uploads
        $upload_dir = __DIR__ . '/../../public/uploads/reports/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0777, true);
        }

        $file_report_pdf = null;
        if (!empty($_FILES['file_report_pdf']['name'])) {
            $file_report_pdf = time() . '_final_' . basename($_FILES['file_report_pdf']['name']);
            @move_uploaded_file($_FILES['file_report_pdf']['tmp_name'], $upload_dir . $file_report_pdf);
        }

        // Insert or Update the final report
        $stmtCheck = $pdo->prepare('SELECT id FROM final_reports WHERE project_id = ?');
        $stmtCheck->execute([$project_id]);
        $existingId = $stmtCheck->fetchColumn();

        if ($existingId) {
            $sql = 'UPDATE final_reports SET 
                    submission_date = ?, executive_summary = ?, utilization_impact = ?, 
                    curriculum_suggestions = ?, faculty_suggestions = ?, status = ?,
                    checklist_report_sent = ?, checklist_budget_cleared = ?, 
                    checklist_outputs_registered = ?, checklist_project_closed = ?, updated_at = NOW()';
            
            $params = [$submission_date, $executive_summary, $utilization_impact, $curriculum_suggestions, $faculty_suggestions, $status, $checklist_report_sent, $checklist_budget_cleared, $checklist_outputs_registered, $checklist_project_closed];

            if ($file_report_pdf) {
                $sql .= ', file_report_pdf = ?';
                $params[] = $file_report_pdf;
            }
            
            $sql .= ' WHERE id = ?';
            $params[] = $existingId;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } else {
            $stmt = $pdo->prepare('
                INSERT INTO final_reports (
                    project_id, submission_date, executive_summary, utilization_impact,
                    curriculum_suggestions, faculty_suggestions, file_report_pdf, status,
                    checklist_report_sent, checklist_budget_cleared, checklist_outputs_registered, checklist_project_closed,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ');
            
            $stmt->execute([
                $project_id, $submission_date, $executive_summary, $utilization_impact,
                $curriculum_suggestions, $faculty_suggestions, $file_report_pdf, $status,
                $checklist_report_sent, $checklist_budget_cleared, $checklist_outputs_registered, $checklist_project_closed
            ]);
        }
        
        // Update project status if submitted
        if ($status === 'submitted') {
            // Check progress before triggering closure request
            $stmtProgress = $pdo->prepare('SELECT COALESCE(SUM(percentage_complete), 0) FROM progress_reports WHERE project_id = ?');
            $stmtProgress->execute([$project_id]);
            $totalProgress = $stmtProgress->fetchColumn();

            if ($totalProgress < 100) {
                // Save report as draft instead and inform user
                $stmtDowngrade = $pdo->prepare('UPDATE final_reports SET status = "draft" WHERE project_id = ?');
                $stmtDowngrade->execute([$project_id]);
                $_SESSION['error_msg'] = "ไม่สามารถรองรับการส่งรายงานฉบับสมบูรณ์เพื่อปิดโครงการได้ เนื่องจากโครงการมีความก้าวหน้าเพียง $totalProgress% (ต้องครบ 100%)";
                redirect('/projects/' . $project_id);
            }

            $updateStmt = $pdo->prepare('UPDATE projects SET closure_requested = 1 WHERE id = ?');
            $updateStmt->execute([$project_id]);
            $_SESSION['alert_msg'] = "ส่งรายงานฉบับสมบูรณ์เรียบร้อยแล้ว รอการอนุมัติปิดโครงการ";
            
            // Notify Admins
            $adminStmt = $pdo->query("SELECT u.id FROM users u JOIN model_has_roles mhr ON u.id = mhr.model_id JOIN roles r ON mhr.role_id = r.id WHERE r.name = 'admin' OR r.name = 'System Administrator'");
            $admins = $adminStmt->fetchAll(PDO::FETCH_COLUMN);
            
            $projStmt = $pdo->prepare("SELECT code FROM projects WHERE id = ?");
            $projStmt->execute([$project_id]);
            $projCode = $projStmt->fetchColumn();

            foreach ($admins as $adminId) {
                addNotification($pdo, $adminId, "ขออนุมัติปิดโครงการ", "โครงการ " . $projCode . " ส่งรายงานฉบับสมบูรณ์และขอปิดโครงการ");
            }
        } else {
            $_SESSION['alert_msg'] = "บันทึกร่างรายงานฉบับสมบูรณ์เรียบร้อยแล้ว";
        }

        redirect('/projects/' . $project_id);
    }
}
