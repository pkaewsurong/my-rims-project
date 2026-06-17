<?php
// main/ajax/projects/DeleteIP.php - ลบข้อมูลทรัพย์สินทางปัญญา
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
$stmt = $pdo->prepare('SELECT ip.*, proj.proposal_id, prop.user_id FROM ip_creations ip JOIN projects proj ON ip.project_id = proj.id JOIN proposals prop ON proj.proposal_id = prop.id WHERE ip.id = ?');
$stmt->execute([$id]);
$ip = $stmt->fetch();

if (!$ip || (!hasRole('admin') && $ip['user_id'] != authUser()['id'])) {
    echo json_encode(['result' => 0, 'message' => 'Access Denied']);
    exit;
}

// Delete Certificate PDF from disk
if ($ip['file_certificate']) {
    $filepath = __DIR__ . '/../../../public/uploads/ip/' . $ip['file_certificate'];
    if (file_exists($filepath)) {
        @unlink($filepath);
    }
}

// Delete from DB
$stmt = $pdo->prepare('DELETE FROM ip_creations WHERE id = ?');
$stmt->execute([$id]);

echo json_encode(['result' => 1]);
