<?php
// main/ajax/archives/SaveArchive.php - บันทึกการอัปโหลดเข้าคลังข้อมูลวิจัย
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }

$proposal_id = (int)($_POST['proposal_id'] ?? 0);
$dataset_name = trim($_POST['dataset_name'] ?? '');
$data_type = trim($_POST['data_type'] ?? '');
$description = trim($_POST['description'] ?? '');
$access_level = trim($_POST['access_level'] ?? 'private');

if (!$proposal_id || !$dataset_name || empty($_FILES['file_path']['name'])) {
    echo json_encode(['result' => 0, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// Check if proposal/project belongs to current user or is admin
$stmt = $pdo->prepare('SELECT id, user_id FROM proposals WHERE id = ?');
$stmt->execute([$proposal_id]);
$prop = $stmt->fetch();

if (!$prop || (!hasRole('admin') && $prop['user_id'] != authUser()['id'])) {
    echo json_encode(['result' => 0, 'message' => 'Access Denied']);
    exit;
}

// Upload file
$upload_dir = __DIR__ . '/../../../public/uploads/archives/';
if (!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0777, true);
}

$file_size = $_FILES['file_path']['size'];
$filename = time() . '_archive_' . basename($_FILES['file_path']['name']);

if (@move_uploaded_file($_FILES['file_path']['tmp_name'], $upload_dir . $filename)) {
    $stmt = $pdo->prepare('
        INSERT INTO data_archives (
            proposal_id, dataset_name, data_type, description, 
            file_path, file_size, access_level, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ');
    $stmt->execute([
        $proposal_id, $dataset_name, $data_type, $description,
        $filename, $file_size, $access_level
    ]);
    
    echo json_encode(['result' => 1]);
} else {
    echo json_encode(['result' => 0, 'message' => 'ไม่สามารถย้ายไฟล์อัปโหลดได้']);
}
