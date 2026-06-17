<?php
// main/ajax/projects/UploadProjectFile.php - อัปโหลดไฟล์เอกสารแนบโครงการ
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }

$project_id = (int)($_POST['project_id'] ?? 0);
$file_type = trim($_POST['file_type'] ?? '');

if (!$project_id || !$file_type || empty($_FILES['file_path']['name'])) {
    echo json_encode(['result' => 0, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// Check access
$stmt = $pdo->prepare('SELECT p.*, pr.user_id FROM projects p JOIN proposals pr ON p.proposal_id = pr.id WHERE p.id = ?');
$stmt->execute([$project_id]);
$proj = $stmt->fetch();

if (!$proj || (!hasRole('admin') && $proj['user_id'] != authUser()['id'])) {
    echo json_encode(['result' => 0, 'message' => 'Access Denied']);
    exit;
}

// Upload file
$upload_dir = __DIR__ . '/../../../public/uploads/project_files/';
if (!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0777, true);
}

$filename = time() . '_projfile_' . basename($_FILES['file_path']['name']);
if (@move_uploaded_file($_FILES['file_path']['tmp_name'], $upload_dir . $filename)) {
    $db_path = 'uploads/project_files/' . $filename;
    
    $stmt = $pdo->prepare('
        INSERT INTO project_files (project_id, file_type, file_path, upload_date, created_at, updated_at) 
        VALUES (?, ?, ?, NOW(), NOW(), NOW())
    ');
    $stmt->execute([$project_id, $file_type, $db_path]);
    
    echo json_encode(['result' => 1]);
} else {
    echo json_encode(['result' => 0, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์']);
}
