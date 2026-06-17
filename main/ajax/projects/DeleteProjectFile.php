<?php
// main/ajax/projects/DeleteProjectFile.php - ลบไฟล์เอกสารโครงการ
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }

$id = (int)($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['result' => 0, 'message' => 'Invalid ID']);
    exit;
}

// Check access
$stmt = $pdo->prepare('SELECT pf.*, proj.proposal_id, prop.user_id FROM project_files pf JOIN projects proj ON pf.project_id = proj.id JOIN proposals prop ON proj.proposal_id = prop.id WHERE pf.id = ?');
$stmt->execute([$id]);
$file = $stmt->fetch();

if (!$file || (!hasRole('admin') && $file['user_id'] != authUser()['id'])) {
    echo json_encode(['result' => 0, 'message' => 'Access Denied']);
    exit;
}

// Delete file from disk
$filepath = __DIR__ . '/../../../public/' . $file['file_path'];
if (file_exists($filepath)) {
    @unlink($filepath);
}

// Delete from DB
$stmt = $pdo->prepare('DELETE FROM project_files WHERE id = ?');
$stmt->execute([$id]);

echo json_encode(['result' => 1]);
