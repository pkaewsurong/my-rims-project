<?php
// main/ajax/projects/DeleteProgress.php - ลบรายงานความก้าวหน้า
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }

$id = (int)($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['result' => 0, 'message' => 'Invalid ID']);
    exit;
}

// Check existence and ownership
$stmt = $pdo->prepare('SELECT pr.*, p.proposal_id, prop.user_id FROM progress_reports pr JOIN projects p ON pr.project_id = p.id JOIN proposals prop ON p.proposal_id = prop.id WHERE pr.id = ?');
$stmt->execute([$id]);
$rep = $stmt->fetch();

if (!$rep || (!hasRole('admin') && $rep['user_id'] != authUser()['id'])) {
    echo json_encode(['result' => 0, 'message' => 'Access Denied']);
    exit;
}

// Delete attachment file from filesystem
if ($rep['attachment_path']) {
    $filepath = __DIR__ . '/../../../public/uploads/progress/' . $rep['attachment_path'];
    if (file_exists($filepath)) {
        @unlink($filepath);
    }
}

// Delete from DB
$stmt = $pdo->prepare('DELETE FROM progress_reports WHERE id = ?');
$stmt->execute([$id]);

echo json_encode(['result' => 1]);
