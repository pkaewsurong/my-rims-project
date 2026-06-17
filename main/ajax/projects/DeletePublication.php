<?php
// main/ajax/projects/DeletePublication.php - ลบผลงานตีพิมพ์
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
$stmt = $pdo->prepare('SELECT p.*, proj.proposal_id, prop.user_id FROM publications p JOIN projects proj ON p.project_id = proj.id JOIN proposals prop ON proj.proposal_id = prop.id WHERE p.id = ?');
$stmt->execute([$id]);
$pub = $stmt->fetch();

if (!$pub || (!hasRole('admin') && $pub['user_id'] != authUser()['id'])) {
    echo json_encode(['result' => 0, 'message' => 'Access Denied']);
    exit;
}

// Delete PDF from disk
if ($pub['file_full_text']) {
    $filepath = __DIR__ . '/../../../public/uploads/publications/' . $pub['file_full_text'];
    if (file_exists($filepath)) {
        @unlink($filepath);
    }
}

// Delete from DB
$stmt = $pdo->prepare('DELETE FROM publications WHERE id = ?');
$stmt->execute([$id]);

echo json_encode(['result' => 1]);
