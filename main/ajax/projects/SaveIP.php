<?php
// main/ajax/projects/SaveIP.php - บันทึกข้อมูลสิทธิบัตร/ทรัพย์สินทางปัญญา
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { echo json_encode(['result' => 0, 'message' => 'Unauthorized']); exit; }

$project_id = (int)($_POST['project_id'] ?? 0);
$name_th = trim($_POST['name_th'] ?? '');
$name_en = trim($_POST['name_en'] ?? '');
$ip_type = trim($_POST['ip_type'] ?? 'Patent');
$completion_date = !empty($_POST['completion_date']) ? $_POST['completion_date'] : null;
$legal_status = trim($_POST['legal_status'] ?? '');
$registration_number = trim($_POST['registration_number'] ?? '');
$registration_agency = trim($_POST['registration_agency'] ?? '');
$approval_date = !empty($_POST['approval_date']) ? $_POST['approval_date'] : null;
$economic_value = (float)($_POST['economic_value'] ?? 0);
$keywords = trim($_POST['keywords'] ?? '');
$abstract_details = trim($_POST['abstract_details'] ?? '');

$researcher_id = authUser()['id'];

if (!$project_id || !$name_th || !$completion_date) {
    echo json_encode(['result' => 0, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// Handle File upload
$file_certificate = null;
if (!empty($_FILES['file_certificate']['name'])) {
    $upload_dir = __DIR__ . '/../../../public/uploads/ip/';
    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, 0777, true);
    }
    $filename = time() . '_ip_' . basename($_FILES['file_certificate']['name']);
    if (@move_uploaded_file($_FILES['file_certificate']['tmp_name'], $upload_dir . $filename)) {
        $file_certificate = $filename;
    }
}

// Insert into ip_creations
$stmt = $pdo->prepare('
    INSERT INTO ip_creations (
        project_id, researcher_id, name_th, name_en, ip_type, completion_date,
        legal_status, registration_number, registration_agency, approval_date,
        economic_value, keywords, abstract_details, file_certificate, created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
');

$stmt->execute([
    $project_id, $researcher_id, $name_th, $name_en, $ip_type, $completion_date,
    $legal_status, $registration_number, $registration_agency, $approval_date,
    $economic_value, $keywords, $abstract_details, $file_certificate
]);

echo json_encode(['result' => 1]);
