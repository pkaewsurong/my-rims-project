<?php
// main/ajax/projects/UpdateMilestoneStatus.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }

$id = (int)($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? 'pending');

if (!$id) {
    echo json_encode(['result' => 0, 'message' => 'Invalid ID']);
    exit;
}

// Check milestone existence and user rights
$stmt = $pdo->prepare('SELECT pm.*, pr.user_id FROM project_milestones pm JOIN projects p ON pm.project_id = p.id JOIN proposals pr ON p.proposal_id = pr.id WHERE pm.id = ?');
$stmt->execute([$id]);
$ms = $stmt->fetch();

if (!$ms || (!hasRole('admin') && $ms['user_id'] != authUser()['id'])) {
    echo json_encode(['result' => 0, 'message' => 'Access Denied']);
    exit;
}

$stmt = $pdo->prepare('UPDATE project_milestones SET status = ?, updated_at = NOW() WHERE id = ?');
$stmt->execute([$status, $id]);

echo json_encode(['result' => 1]);
