<?php
// main/ajax/projects/ApproveClosure.php - อนุมัติการปิดโครงการ (Admin)
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { header('Location: ../../login.php'); exit; }
requireRole('admin');

$id = (int)($_GET['id'] ?? 0);
if (!$id) { die("Project ID is required"); }

// Fetch project and owner details
$stmt = $pdo->prepare('SELECT p.*, pr.user_id, pr.title FROM projects p JOIN proposals pr ON p.proposal_id = pr.id WHERE p.id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) { die("Project not found"); }

// Update status to closed
$stmtUpdate = $pdo->prepare('UPDATE projects SET status = "closed", closure_requested = 0 WHERE id = ?');
$stmtUpdate->execute([$id]);

// Update proposal status to closed
$stmtProp = $pdo->prepare('UPDATE proposals SET status = "closed" WHERE id = ?');
$stmtProp->execute([$project['proposal_id']]);

// Notify owner
addNotification($pdo, $project['user_id'], "โครงการถูกปิดแล้ว", "โครงการ " . $project['title'] . " ได้รับการอนุมัติปิดโครงการเสร็จสมบูรณ์แล้ว");

$_SESSION['alert_msg'] = "อนุมัติปิดโครงการสำเร็จ";
header('Location: ../../project_detail.php?id=' . $id);
exit;
