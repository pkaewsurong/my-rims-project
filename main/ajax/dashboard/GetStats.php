<?php
// main/ajax/dashboard/GetStats.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$user_id = authUser()['id'];
$isAdmin = hasRole('admin');

// Count my projects
$stmt = $pdo->prepare('SELECT COUNT(*) FROM projects p JOIN proposals pr ON p.proposal_id = pr.id WHERE pr.user_id = ?');
$stmt->execute([$user_id]);
$myProjects = $stmt->fetchColumn();

// Count my ongoing projects
$stmt = $pdo->prepare('SELECT COUNT(*) FROM projects p JOIN proposals pr ON p.proposal_id = pr.id WHERE pr.user_id = ? AND p.status NOT IN ("closed","completed")');
$stmt->execute([$user_id]);
$ongoingProjects = $stmt->fetchColumn();

// Count my proposals
$stmt = $pdo->prepare('SELECT COUNT(*) FROM proposals WHERE user_id = ? AND status != "closed"');
$stmt->execute([$user_id]);
$myProposals = $stmt->fetchColumn();

// Total budget
$stmt = $pdo->prepare('SELECT COALESCE(SUM(pr.budget_total),0) FROM projects p JOIN proposals pr ON p.proposal_id = pr.id WHERE pr.user_id = ?');
$stmt->execute([$user_id]);
$totalBudget = (float)$stmt->fetchColumn();

// Admin stats
$totalResearchers = 0;
$pendingReviews = 0;
if ($isAdmin) {
    $totalResearchers = $pdo->query("SELECT COUNT(DISTINCT u.id) FROM users u JOIN model_has_roles mhr ON u.id = mhr.model_id JOIN roles r ON mhr.role_id = r.id WHERE r.name IN ('Researcher','Research Admin')")->fetchColumn();
    $pendingReviews = $pdo->query("SELECT COUNT(*) FROM proposals WHERE status = 'submitted'")->fetchColumn();
}

header('Content-Type: application/json');
echo json_encode([
    'myProjects'       => $myProjects,
    'ongoingProjects'  => $ongoingProjects,
    'myProposals'      => $myProposals,
    'totalBudget'      => $totalBudget,
    'totalResearchers' => $totalResearchers,
    'pendingReviews'   => $pendingReviews,
]);
