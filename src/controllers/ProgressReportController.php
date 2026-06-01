<?php
// src/controllers/ProgressReportController.php

function progressCreateAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $project_id = $_GET['project_id'] ?? null;
    
    if (!$project_id) {
        die("Project ID is required");
    }

    // Verify project exists
    $stmt = $pdo->prepare('
        SELECT p.id, pr.title, p.code, p.start_date, p.end_date, pr.budget_total,
               COALESCE((SELECT SUM(amount) FROM proposal_budget_items WHERE proposal_id = p.proposal_id), 0) as budget_used
        FROM projects p 
        LEFT JOIN proposals pr ON p.proposal_id = pr.id 
        WHERE p.id = ?
    ');
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();

    if (!$project) {
        die("Project not found");
    }

    // Fetch Milestones
    $stmt = $pdo->prepare('SELECT * FROM project_milestones WHERE project_id = ? ORDER BY id ASC');
    $stmt->execute([$project_id]);
    $milestones = $stmt->fetchAll();

    // Calculate planned progress (mock logic for demo if no start/end date, or based on milestones)
    $stmt = $pdo->prepare('SELECT SUM(percentage_complete) as total_progress FROM progress_reports WHERE project_id = ?');
    $stmt->execute([$project_id]);
    $current_progress = $stmt->fetchColumn() ?: 0;
    
    // Calculate planned progress based on time elapsed
    $planned_progress = 0;
    if ($project['start_date'] && $project['end_date']) {
        $start = new DateTime($project['start_date']);
        $end = new DateTime($project['end_date']);
        $now = new DateTime();
        
        if ($now > $start) {
            $total_days = $start->diff($end)->days ?: 1;
            $elapsed_days = $start->diff($now)->days;
            $planned_progress = min(100, round(($elapsed_days / $total_days) * 100));
        }
    }

    require __DIR__ . '/../../views/progress/create.php';
}

function progressStoreAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project_id = $_POST['project_id'] ?? null;
        $report_period = $_POST['report_period'] ?? '';
        $percentage_complete = $_POST['percentage_complete'] ?? 0;
        $summary_text = $_POST['summary_text'] ?? '';
        $problems_obstacles = $_POST['problems_obstacles'] ?? '';
        $next_milestone_plan = $_POST['next_milestone_plan'] ?? '';
        $risk_level = $_POST['risk_level'] ?? 'Low';
        $planned_progress_percentage = $_POST['planned_progress_percentage'] ?? 0;
        $milestones = $_POST['milestones'] ?? [];

        if (!$project_id || empty($report_period)) {
            die("Required fields missing");
        }

        // Handle file upload
        $attachment_paths = [];
        if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'][0])) {
            $upload_dir = __DIR__ . '/../../public/uploads/progress/';
            if (!is_dir($upload_dir)) {
                @mkdir($upload_dir, 0777, true);
            }
            
            foreach ($_FILES['attachment']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['attachment']['error'][$key] === UPLOAD_ERR_OK) {
                    $original_filename = basename($_FILES['attachment']['name'][$key]);
                    $filename = uniqid() . '_' . $original_filename;
                    if (@move_uploaded_file($tmp_name, $upload_dir . $filename)) {
                        $attachment_paths[] = [
                            'path' => 'uploads/progress/' . $filename,
                            'name' => $original_filename
                        ];
                    }
                }
            }
        }
        $attachment_json = !empty($attachment_paths) ? json_encode($attachment_paths, JSON_UNESCAPED_UNICODE) : null;

        $stmt = $pdo->prepare('
            INSERT INTO progress_reports (project_id, report_period, percentage_complete, planned_progress_percentage, risk_level, summary_text, problems_obstacles, next_milestone_plan, attachment_path, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ');
        
        $stmt->execute([$project_id, $report_period, $percentage_complete, $planned_progress_percentage, $risk_level, $summary_text, $problems_obstacles, $next_milestone_plan, $attachment_json]);
        
        // Update milestone statuses if provided
        if (!empty($milestones)) {
            foreach ($milestones as $milestone_id => $status) {
                // simple validation
                if (in_array($status, ['pending', 'in_progress', 'completed'])) {
                    $mStmt = $pdo->prepare('UPDATE project_milestones SET status = ?, updated_at = NOW() WHERE id = ? AND project_id = ?');
                    $mStmt->execute([$status, $milestone_id, $project_id]);
                }
            }
        }
        
        // Update project status if cumulative progress reaches 100%
        $sumStmt = $pdo->prepare('SELECT SUM(percentage_complete) FROM progress_reports WHERE project_id = ?');
        $sumStmt->execute([$project_id]);
        $total_percentage = $sumStmt->fetchColumn() ?: 0;
        
        if ($total_percentage >= 100) {
            $stmt = $pdo->prepare('UPDATE projects SET status = "completed" WHERE id = ?');
            $stmt->execute([$project_id]);
        }

        redirect('/projects/' . $project_id);
    }
}
