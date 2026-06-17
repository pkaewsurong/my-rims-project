<?php
// main/ajax/projects/DeleteDraft.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { echo json_encode(['result'=>0,'message'=>'Unauthorized']); exit; }

header('Content-Type: application/json');
$proposal_id = (int)($_POST['proposal_id'] ?? 0);
$user_id = authUser()['id'];

$stmt = $pdo->prepare('SELECT * FROM proposals WHERE id = ? AND user_id = ? AND status = "draft"');
$stmt->execute([$proposal_id, $user_id]);
$proposal = $stmt->fetch();

if (!$proposal) { echo json_encode(['result'=>0,'message'=>'ไม่พบข้อเสนอ หรือไม่มีสิทธิ์ลบ']); exit; }

// Delete files
$uploadDir = __DIR__ . '/../../../public/uploads/proposals/';
foreach (['file_proposal','file_budget','file_cv','file_ethics'] as $field) {
    if (!empty($proposal[$field]) && file_exists($uploadDir . $proposal[$field])) {
        unlink($uploadDir . $proposal[$field]);
    }
}

$pdo->prepare('DELETE FROM proposal_teams WHERE proposal_id = ?')->execute([$proposal_id]);
$pdo->prepare('DELETE FROM proposals WHERE id = ?')->execute([$proposal_id]);

echo json_encode(['result'=>1]);
