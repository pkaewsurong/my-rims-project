<?php
// main/ajax/projects/SaveOutput.php - บันทึกผลงานตีพิมพ์
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }

$project_id = (int)($_POST['project_id'] ?? 0);
$title_th = trim($_POST['title_th'] ?? '');
$title_en = trim($_POST['title_en'] ?? '');
$publication_type = trim($_POST['publication_type'] ?? 'International Journal');
$publish_year = trim($_POST['publish_year'] ?? '');
$journal_name = trim($_POST['journal_name'] ?? '');
$issn = trim($_POST['issn'] ?? '');
$volume = trim($_POST['volume'] ?? '');
$issue = trim($_POST['issue'] ?? '');
$page_length = trim($_POST['page_length'] ?? '');
$indexing_database = trim($_POST['indexing_database'] ?? '');
$quartile = trim($_POST['quartile'] ?? '');
$impact_factor = trim($_POST['impact_factor'] ?? '');
$doi_url = trim($_POST['doi_url'] ?? '');
$utilization_summary = trim($_POST['utilization_summary'] ?? '');

$researcher_id = authUser()['id'];

if (!$project_id || !$title_th || !$publish_year || !$journal_name) {
    echo json_encode(['result' => 0, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// Handle PDF upload
$file_full_text = null;
if (!empty($_FILES['file_full_text']['name'])) {
    $upload_dir = __DIR__ . '/../../../public/uploads/publications/';
    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, 0777, true);
    }
    $filename = time() . '_pub_' . basename($_FILES['file_full_text']['name']);
    if (@move_uploaded_file($_FILES['file_full_text']['tmp_name'], $upload_dir . $filename)) {
        $file_full_text = $filename;
    }
}

// Insert into publications
$stmt = $pdo->prepare('
    INSERT INTO publications (
        project_id, researcher_id, title_th, title_en, publication_type,
        publish_year, journal_name, issn, volume, issue, page_length,
        indexing_database, quartile, impact_factor, doi_url, utilization_summary,
        file_full_text, status, created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "published", NOW(), NOW())
');

$stmt->execute([
    $project_id, $researcher_id, $title_th, $title_en, $publication_type,
    $publish_year, $journal_name, $issn, $volume, $issue, $page_length,
    $indexing_database, $quartile, $impact_factor, $doi_url, $utilization_summary,
    $file_full_text
]);

echo json_encode(['result' => 1]);
