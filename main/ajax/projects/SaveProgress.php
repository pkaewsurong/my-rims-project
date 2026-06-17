<?php
// main/ajax/projects/SaveProgress.php - บันทึกรายงานความก้าวหน้า
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }

$project_id = (int)($_POST['project_id'] ?? 0);
$report_period = trim($_POST['report_period'] ?? '');
$percentage_complete = (int)($_POST['percentage_complete'] ?? 0);
$risk_level = trim($_POST['risk_level'] ?? 'Low');
$summary_text = trim($_POST['summary_text'] ?? '');
$problems_obstacles = trim($_POST['problems_obstacles'] ?? '');
$next_milestone_plan = trim($_POST['next_milestone_plan'] ?? '');

if (!$project_id || !$report_period || !$summary_text) {
    echo json_encode(['result' => 0, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// Handle File Upload
$attachment_path = null;
if (!empty($_FILES['attachment']['name'])) {
    $upload_dir = __DIR__ . '/../../../public/uploads/progress/';
    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, 0777, true);
    }
    $filename = time() . '_progress_' . basename($_FILES['attachment']['name']);
    if (@move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_dir . $filename)) {
        $attachment_path = $filename;
    }
}

// Save Progress Report
$stmt = $pdo->prepare('
    INSERT INTO progress_reports (
        project_id, report_period, percentage_complete, risk_level, 
        summary_text, problems_obstacles, next_milestone_plan, 
        attachment_path, status, created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, "submitted", NOW(), NOW())
');
$stmt->execute([
    $project_id, $report_period, $percentage_complete, $risk_level,
    $summary_text, $problems_obstacles, $next_milestone_plan,
    $attachment_path
]);

// Update milestones if passed
$milestones = $_POST['milestones'] ?? [];
if (!empty($milestones)) {
    $stmtMs = $pdo->prepare('UPDATE project_milestones SET status = ?, updated_at = NOW() WHERE id = ? AND project_id = ?');
    foreach ($milestones as $msId => $status) {
        $stmtMs->execute([$status, (int)$msId, $project_id]);
    }
}

echo json_encode(['result' => 1]);
