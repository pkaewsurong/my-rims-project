<?php
// main/ajax/admin/SaveMasterData.php - บันทึกข้อมูลหลักเข้าฐานข้อมูล
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }
requireRole('admin');

header('Content-Type: application/json');

$tab = trim($_POST['tab'] ?? 'funders');

if ($tab === 'funders') {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? 'External');
    $status = trim($_POST['status'] ?? 'Active');
    
    if (empty($name)) {
        echo json_encode(['result' => 0, 'message' => 'กรุณากรอกชื่อแหล่งทุน']);
        exit;
    }
    
    $stmt = $pdo->prepare('INSERT INTO funders (name, type, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
    $stmt->execute([$name, $type, $status]);
    echo json_encode(['result' => 1]);
    exit;

} elseif ($tab === 'journals') {
    $name = trim($_POST['name'] ?? '');
    $issn = trim($_POST['issn'] ?? '');
    $quartile = trim($_POST['quartile'] ?? 'N/A');
    $database_index = trim($_POST['database_index'] ?? 'Other');
    
    if (empty($name)) {
        echo json_encode(['result' => 0, 'message' => 'กรุณากรอกชื่อวารสาร']);
        exit;
    }
    
    $stmt = $pdo->prepare('INSERT INTO journals (name, issn, quartile, database_index, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([$name, $issn, $quartile, $database_index]);
    echo json_encode(['result' => 1]);
    exit;

} elseif ($tab === 'tiers') {
    $category = trim($_POST['category'] ?? 'Journal');
    $level_name = trim($_POST['level_name'] ?? '');
    $points = (float)($_POST['points'] ?? 1.0);
    $description = trim($_POST['description'] ?? '');
    
    if (empty($level_name)) {
        echo json_encode(['result' => 0, 'message' => 'กรุณากรอกชื่อระดับคะแนน']);
        exit;
    }
    
    $stmt = $pdo->prepare('INSERT INTO metric_tiers (category, level_name, description, points, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([$category, $level_name, $description, $points]);
    echo json_encode(['result' => 1]);
    exit;
}

echo json_encode(['result' => 0, 'message' => 'Invalid tab parameter']);
