<?php
// main/ajax/projects/RequestClosure.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { header('Location: ../../login.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('Project ID required');

$user_id = authUser()['id'];
$stmt = $pdo->prepare("
    SELECT p.*, pr.user_id,
    COALESCE((SELECT (COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100) / NULLIF(COUNT(*), 0) FROM project_milestones WHERE project_id = p.id), 0) as total_progress,
    (SELECT status FROM final_reports WHERE project_id = p.id LIMIT 1) as final_report_status
    FROM projects p JOIN proposals pr ON p.proposal_id = pr.id WHERE p.id = ?
");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project || $project['user_id'] != $user_id) die('Unauthorized');
if (($project['total_progress'] ?? 0) < 100) { header('Location: ../../projects.php?error=progress'); exit; }
if (!in_array($project['final_report_status'] ?? '', ['submitted','approved'])) { header('Location: ../../projects.php?error=report'); exit; }

$pdo->prepare('UPDATE projects SET closure_requested = 1 WHERE id = ?')->execute([$id]);

// Notify admins
$admins = $pdo->query("SELECT u.id FROM users u JOIN model_has_roles mhr ON u.id=mhr.model_id JOIN roles r ON mhr.role_id=r.id WHERE r.name IN ('admin','System Administrator')")->fetchAll(PDO::FETCH_COLUMN);
foreach ($admins as $adminId) {
    addNotification($pdo, $adminId, 'คำขอปิดโครงการ', 'โครงการ '.$project['code'].' ได้ส่งคำขอปิดโครงการ');
}

header('Location: ../../projects.php?success=closure_requested');
