<?php
// main/ajax/projects/SaveFinalReport.php - บันทึกรายงานการวิจัยฉบับสมบูรณ์
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }

$project_id = (int)($_POST['project_id'] ?? 0);
$executive_summary = trim($_POST['executive_summary'] ?? '');
$utilization_impact = trim($_POST['utilization_impact'] ?? '');
$curriculum_suggestions = trim($_POST['curriculum_suggestions'] ?? '');
$faculty_suggestions = trim($_POST['faculty_suggestions'] ?? '');

$checklist_report_sent = isset($_POST['checklist_report_sent']) ? 1 : 0;
$checklist_budget_cleared = isset($_POST['checklist_budget_cleared']) ? 1 : 0;
$checklist_outputs_registered = isset($_POST['checklist_outputs_registered']) ? 1 : 0;

if (!$project_id || !$executive_summary || !$utilization_impact || empty($_FILES['file_report_pdf']['name'])) {
    echo json_encode(['result' => 0, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// Upload PDF
$file_report_pdf = null;
$upload_dir = __DIR__ . '/../../../public/uploads/final_reports/';
if (!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0777, true);
}
$filename = time() . '_final_' . basename($_FILES['file_report_pdf']['name']);
if (@move_uploaded_file($_FILES['file_report_pdf']['tmp_name'], $upload_dir . $filename)) {
    $file_report_pdf = $filename;
} else {
    echo json_encode(['result' => 0, 'message' => 'ไม่สามารถอัปโหลดไฟล์ PDF ได้']);
    exit;
}

// Insert into final_reports
$stmt = $pdo->prepare('
    INSERT INTO final_reports (
        project_id, submission_date, executive_summary, utilization_impact,
        curriculum_suggestions, faculty_suggestions, file_report_pdf, status,
        checklist_report_sent, checklist_budget_cleared, checklist_outputs_registered,
        created_at, updated_at
    ) VALUES (?, NOW(), ?, ?, ?, ?, ?, "submitted", ?, ?, ?, NOW(), NOW())
');

$stmt->execute([
    $project_id, $executive_summary, $utilization_impact,
    $curriculum_suggestions, $faculty_suggestions, $file_report_pdf,
    $checklist_report_sent, $checklist_budget_cleared, $checklist_outputs_registered
]);

echo json_encode(['result' => 1]);
