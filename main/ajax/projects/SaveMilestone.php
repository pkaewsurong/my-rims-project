<?php
// main/ajax/projects/SaveMilestone.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }

$project_id = (int)($_POST['project_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$desc = trim($_POST['desc'] ?? '');

if (!$project_id || !$name) {
    echo json_encode(['result' => 0, 'message' => 'กรุณาระบุข้อมูลที่จำเป็น']);
    exit;
}

// Verify ownership/admin status
$stmt = $pdo->prepare('SELECT p.id, pr.user_id FROM projects p JOIN proposals pr ON p.proposal_id = pr.id WHERE p.id = ?');
$stmt->execute([$project_id]);
$proj = $stmt->fetch();

if (!$proj || (!hasRole('admin') && $proj['user_id'] != authUser()['id'])) {
    echo json_encode(['result' => 0, 'message' => 'Access Denied']);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO project_milestones (project_id, milestone_name, description, status, created_at, updated_at) VALUES (?, ?, ?, "pending", NOW(), NOW())');
$stmt->execute([$project_id, $name, $desc]);

echo json_encode(['result' => 1]);
