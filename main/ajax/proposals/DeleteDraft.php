<?php
// main/ajax/proposals/DeleteDraft.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { echo json_encode(['result'=>0,'message'=>'Unauthorized']); exit; }

header('Content-Type: application/json');
$id = (int)($_POST['id'] ?? 0);
$user_id = authUser()['id'];

$stmt = $pdo->prepare('SELECT * FROM proposals WHERE id = ? AND user_id = ? AND status = "draft"');
$stmt->execute([$id, $user_id]);
$proposal = $stmt->fetch();

if (!$proposal) { echo json_encode(['result'=>0,'message'=>'ไม่พบข้อเสนอ หรือไม่มีสิทธิ์ลบ']); exit; }

$uploadDir = __DIR__ . '/../../../public/uploads/proposals/';
foreach (['file_proposal','file_budget','file_cv','file_ethics'] as $field) {
    if (!empty($proposal[$field]) && file_exists($uploadDir . $proposal[$field])) {
        unlink($uploadDir . $proposal[$field]);
    }
}

$pdo->prepare('DELETE FROM proposal_teams WHERE proposal_id = ?')->execute([$id]);
$pdo->prepare('DELETE FROM proposals WHERE id = ?')->execute([$id]);

echo json_encode(['result'=>1]);
