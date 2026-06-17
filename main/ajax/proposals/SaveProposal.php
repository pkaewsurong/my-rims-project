<?php
// main/ajax/proposals/SaveProposal.php - Create/Update proposal via AJAX
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { echo json_encode(['result'=>0,'message'=>'Unauthorized']); exit; }

header('Content-Type: application/json');

$user_id     = authUser()['id'];
$proposal_id = (int)($_POST['proposal_id'] ?? 0);
$status      = in_array($_POST['status'] ?? '', ['draft','submitted']) ? $_POST['status'] : 'draft';

$title           = trim($_POST['title'] ?? '');
$title_en        = trim($_POST['title_en'] ?? '');
$research_type   = trim($_POST['research_type'] ?? '');
$keywords        = trim($_POST['keywords'] ?? '');
$abstract        = trim($_POST['abstract'] ?? '');
$budget_total    = !empty($_POST['budget_total']) ? (float)str_replace(',','',$_POST['budget_total']) : 0;
$funding_source_id = !empty($_POST['funding_source_id']) ? $_POST['funding_source_id'] : null;
$budget_details  = trim($_POST['budget_details'] ?? '');
$start_date      = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date        = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
$strategic_link  = trim($_POST['strategic_link'] ?? '');
$impact_indicator = trim($_POST['impact_indicator'] ?? '');

// Milestones
$milestone_names = $_POST['milestone_name'] ?? [];
$milestone_descs = $_POST['milestone_description'] ?? [];
$milestones_array = [];
for ($i = 0; $i < count($milestone_names); $i++) {
    if (!empty(trim($milestone_names[$i]))) {
        $milestones_array[] = [
            'name' => trim($milestone_names[$i]),
            'description' => trim($milestone_descs[$i] ?? '')
        ];
    }
}
$milestones = !empty($milestones_array) ? json_encode($milestones_array, JSON_UNESCAPED_UNICODE) : '';

// Expected outputs
$expected_outputs = $_POST['expected_outputs'] ?? [];
$expected_outputs_json = is_array($expected_outputs) ? json_encode($expected_outputs, JSON_UNESCAPED_UNICODE) : '[]';

if ($status === 'submitted' && empty($title)) {
    echo json_encode(['result'=>0,'message'=>'กรุณากรอกชื่อโครงการ']);
    exit;
}
if (empty($title)) $title = '(ร่าง) ยังไม่มีชื่อ';

// Calculate PI proportion
$team_proportions = $_POST['team_proportion'] ?? [];
$total_coi = array_sum(array_map('intval', $team_proportions));
$pi_proportion = max(0, 100 - $total_coi);

// Handle file uploads
$uploadDir = __DIR__ . '/../../../public/uploads/proposals/';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);

$fileUpdates = []; $fileValues = [];
foreach (['file_proposal','file_budget','file_cv','file_ethics'] as $field) {
    if (!empty($_FILES[$field]['name'])) {
        $filename = time() . '_' . $field . '_' . basename($_FILES[$field]['name']);
        @move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $filename);
        $fileUpdates[] = "$field = ?";
        $fileValues[]  = $filename;
    }
}

if ($proposal_id) {
    // UPDATE
    $check = $pdo->prepare('SELECT status FROM proposals WHERE id = ? AND user_id = ?');
    $check->execute([$proposal_id, $user_id]);
    $existingStatus = $check->fetchColumn();
    if (!$existingStatus) { echo json_encode(['result'=>0,'message'=>'ไม่มีสิทธิ์แก้ไข']); exit; }
    if (!in_array($existingStatus, ['draft', 'needs_revision'])) {
        echo json_encode(['result'=>0,'message'=>'ไม่อนุญาตให้แก้ไขข้อเสนอโครงการที่มีสถานะนี้']);
        exit;
    }

    $fileSql = !empty($fileUpdates) ? ', ' . implode(', ', $fileUpdates) : '';
    $stmt = $pdo->prepare("UPDATE proposals SET title=?,title_en=?,research_type=?,keywords=?,abstract=?,pi_proportion=?,budget_total=?,funding_source_id=?,budget_details=?,start_date=?,end_date=?,milestones=?,strategic_link=?,impact_indicator=?,expected_outputs=?,status=?,revision_comment=NULL,updated_at=NOW() $fileSql WHERE id=? AND user_id=?");
    $params = [$title,$title_en,$research_type,$keywords,$abstract,$pi_proportion,$budget_total,$funding_source_id,$budget_details,$start_date,$end_date,$milestones,$strategic_link,$impact_indicator,$expected_outputs_json,$status];
    $params = array_merge($params, $fileValues, [$proposal_id, $user_id]);
    $stmt->execute($params);
} else {
    // INSERT
    $stmt = $pdo->prepare("INSERT INTO proposals (title,title_en,research_type,keywords,abstract,pi_proportion,budget_total,funding_source_id,budget_details,start_date,end_date,milestones,strategic_link,impact_indicator,expected_outputs,user_id,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())");
    $stmt->execute([$title,$title_en,$research_type,$keywords,$abstract,$pi_proportion,$budget_total,$funding_source_id,$budget_details,$start_date,$end_date,$milestones,$strategic_link,$impact_indicator,$expected_outputs_json,$user_id,$status]);
    $proposal_id = $pdo->lastInsertId();
}

// Save team members
$pdo->prepare('DELETE FROM proposal_teams WHERE proposal_id = ?')->execute([$proposal_id]);
$team_names = $_POST['team_name'] ?? [];
$team_roles = $_POST['team_role'] ?? [];
if (!empty($team_names)) {
    $teamStmt = $pdo->prepare('INSERT INTO proposal_teams (proposal_id,name,role,proportion,created_at,updated_at) VALUES (?,?,?,?,NOW(),NOW())');
    for ($i = 0; $i < count($team_names); $i++) {
        $name = trim($team_names[$i]);
        if ($name) {
            $teamStmt->execute([$proposal_id, $name, $team_roles[$i] ?? 'Co-Investigator', (int)($team_proportions[$i] ?? 0)]);
        }
    }
}

echo json_encode(['result'=>1,'proposal_id'=>$proposal_id]);
