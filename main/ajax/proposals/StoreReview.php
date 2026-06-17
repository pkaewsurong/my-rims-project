<?php
// main/ajax/proposals/StoreReview.php - บันทึกผลการประเมินและอนุมัติโครงการ
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }
requireRole('admin');

header('Content-Type: application/json');

$proposal_id = (int)($_POST['proposal_id'] ?? 0);
$reviewer_id = authUser()['id'];

$score_concept = (int)($_POST['score_concept'] ?? 0);
$score_team = (int)($_POST['score_team'] ?? 0);
$score_alignment = (int)($_POST['score_alignment'] ?? 0);
$score_impact = (int)($_POST['score_impact'] ?? 0);

$total_score = $score_concept + $score_team + $score_alignment + $score_impact;

$comments_strengths = trim($_POST['comments_strengths'] ?? '');
$comments_suggestions = trim($_POST['comments_suggestions'] ?? '');
$revision_comment = trim($_POST['revision_comment'] ?? '');

$status = trim($_POST['status'] ?? 'under_review');
$is_draft = isset($_POST['is_draft']) && $_POST['is_draft'] === '1' ? 1 : 0;

if ($is_draft) {
    $status = 'under_review'; // Force status to under_review if draft checkbox is ticked
}

if (!$proposal_id) {
    echo json_encode(['result' => 0, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// Check if proposal status is already approved
$propCheck = $pdo->prepare('SELECT status, title, user_id FROM proposals WHERE id = ?');
$propCheck->execute([$proposal_id]);
$proposalData = $propCheck->fetch(PDO::FETCH_ASSOC);

if ($proposalData && $proposalData['status'] === 'approved') {
    echo json_encode(['result' => 0, 'message' => 'ข้อเสนอโครงการนี้ได้รับการอนุมัติไปแล้ว ไม่สามารถประเมินซ้ำได้']);
    exit;
}

// Upsert review record
$checkStmt = $pdo->prepare('SELECT id FROM project_reviews WHERE proposal_id = ? AND reviewer_id = ?');
$checkStmt->execute([$proposal_id, $reviewer_id]);
$existingReview = $checkStmt->fetch();

if ($existingReview) {
    // Update
    $stmt = $pdo->prepare('
        UPDATE project_reviews 
        SET score_concept = ?, score_team = ?, score_alignment = ?, score_impact = ?, 
            total_score = ?, comments_strengths = ?, comments_suggestions = ?, 
            status = ?, updated_at = NOW() 
        WHERE id = ?
    ');
    $stmt->execute([
        $score_concept, $score_team, $score_alignment, $score_impact,
        $total_score, $comments_strengths, $comments_suggestions,
        $status, $existingReview['id']
    ]);
} else {
    // Insert
    $stmt = $pdo->prepare('
        INSERT INTO project_reviews (
            proposal_id, reviewer_id, score_concept, score_team, 
            score_alignment, score_impact, total_score, 
            comments_strengths, comments_suggestions, status, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ');
    $stmt->execute([
        $proposal_id, $reviewer_id, $score_concept, $score_team,
        $score_alignment, $score_impact, $total_score,
        $comments_strengths, $comments_suggestions, $status
    ]);
}

// Update proposal status & revision comment
if ($status === 'needs_revision') {
    $updateStmt = $pdo->prepare('UPDATE proposals SET status = ?, revision_comment = ?, updated_at = NOW() WHERE id = ?');
    $updateStmt->execute([$status, $revision_comment, $proposal_id]);
    
    // Notify owner
    addNotification($pdo, $proposalData['user_id'], "ข้อเสนอโครงการส่งกลับแก้ไข", "ข้อเสนอโครงการ " . $proposalData['title'] . " ถูกส่งกลับให้แก้ไขปรับปรุง");
} else {
    $updateStmt = $pdo->prepare('UPDATE proposals SET status = ?, revision_comment = NULL, updated_at = NOW() WHERE id = ?');
    $updateStmt->execute([$status, $proposal_id]);
}

// If proposal is approved, generate a project code and create the project record
if ($status === 'approved') {
    $checkProj = $pdo->prepare('SELECT id FROM projects WHERE proposal_id = ?');
    $checkProj->execute([$proposal_id]);
    if (!$checkProj->fetch()) {
        // Calculate new code PROJ-XXXX
        $maxCode = $pdo->query("SELECT MAX(CAST(SUBSTRING(code, 6) AS UNSIGNED)) FROM projects")->fetchColumn() ?: 0;
        $newCode = 'PROJ-' . str_pad($maxCode + 1, 4, '0', STR_PAD_LEFT);

        // Fetch details from proposal
        $propStmt = $pdo->prepare('SELECT start_date, end_date, milestones FROM proposals WHERE id = ?');
        $propStmt->execute([$proposal_id]);
        $propData = $propStmt->fetch(PDO::FETCH_ASSOC);

        $startDate = $propData['start_date'] ?? date('Y-m-d');
        $endDate = $propData['end_date'] ?? date('Y-m-d', strtotime('+1 year'));

        // Insert project
        $createProj = $pdo->prepare('
            INSERT INTO projects (proposal_id, code, start_date, end_date, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, "ongoing", NOW(), NOW())
        ');
        $createProj->execute([$proposal_id, $newCode, $startDate, $endDate]);
        $new_project_id = $pdo->lastInsertId();

        // Clone milestones
        if (!empty($propData['milestones'])) {
            $milestones_array = json_decode($propData['milestones'], true);
            if (is_array($milestones_array)) {
                $msStmt = $pdo->prepare('
                    INSERT INTO project_milestones (project_id, milestone_name, description, status, created_at, updated_at) 
                    VALUES (?, ?, ?, "pending", NOW(), NOW())
                ');
                foreach ($milestones_array as $ms) {
                    $msName = $ms['name'] ?? '';
                    $msDesc = $ms['description'] ?? '';
                    if (!empty($msName)) {
                        $msStmt->execute([$new_project_id, $msName, $msDesc]);
                    }
                }
            }
        }
    }
    // Notify owner
    addNotification($pdo, $proposalData['user_id'], "ข้อเสนอได้รับการอนุมัติ", "ข้อเสนอโครงการ " . $proposalData['title'] . " ได้รับการอนุมัติแล้ว และเปลี่ยนเป็นโครงการวิจัยเรียบร้อยแล้ว");
} else if ($status === 'rejected') {
    // Notify owner of rejection
    addNotification($pdo, $proposalData['user_id'], "ข้อเสนอโครงการไม่อนุมัติ", "ข้อเสนอโครงการ " . $proposalData['title'] . " ไม่ได้รับการอนุมัติโครงการ");
}

echo json_encode(['result' => 1]);
