<?php
// src/controllers/IpController.php

function createAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $project_id = $_GET['project_id'] ?? null;
    if (!$project_id) {
        die("Project ID is required");
    }

    $stmt = $pdo->prepare('SELECT p.*, pr.title as title_th, pr.user_id FROM projects p JOIN proposals pr ON p.proposal_id = pr.id WHERE p.id = ?');
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();

    if (!$project) {
        die("Project not found");
    }

    // Verify ownership
    if ($project['user_id'] != authUser()['id'] && !hasRole('admin')) {
        die("Unauthorized");
    }

    require __DIR__ . '/../../views/ip/create.php';
}

function storeAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project_id = $_POST['project_id'] ?? null;
        $ip_type = $_POST['ip_type'] ?? 'Patent'; 
        $name_th = $_POST['name_th'] ?? '';
        $name_en = $_POST['name_en'] ?? '';
        $abstract_details = $_POST['abstract_details'] ?? '';
        $completion_date = $_POST['completion_date'] ?? null;
        $keywords = $_POST['keywords'] ?? '';
        $legal_status = $_POST['legal_status'] ?? '';
        $registration_number = $_POST['registration_number'] ?? '';
        $registration_agency = $_POST['registration_agency'] ?? '';
        $approval_date = $_POST['approval_date'] ?? null;
        $commercial_status = $_POST['commercial_status'] ?? '';
        $economic_value = str_replace(',', '', $_POST['economic_value'] ?? '0');
        $impact_description = $_POST['impact_description'] ?? '';
        
        $inventors_names = $_POST['inventor_name'] ?? [];
        $inventors_proportions = $_POST['inventor_proportion'] ?? [];

        if (!$project_id || empty($ip_type) || empty($name_th)) {
            die("Invalid data: IP Type and Title (TH) are required.");
        }
        
        // Handle File Uploads
        $upload_dir = __DIR__ . '/../../public/uploads/ip/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_submission = null;
        if (!empty($_FILES['file_submission']['name'])) {
            $file_submission = time() . '_sub_' . basename($_FILES['file_submission']['name']);
            move_uploaded_file($_FILES['file_submission']['tmp_name'], $upload_dir . $file_submission);
        }

        $file_certificate = null;
        if (!empty($_FILES['file_certificate']['name'])) {
            $file_certificate = time() . '_cert_' . basename($_FILES['file_certificate']['name']);
            move_uploaded_file($_FILES['file_certificate']['tmp_name'], $upload_dir . $file_certificate);
        }

        $file_evidence = null;
        if (!empty($_FILES['file_evidence']['name'])) {
            $file_evidence = time() . '_evid_' . basename($_FILES['file_evidence']['name']);
            move_uploaded_file($_FILES['file_evidence']['tmp_name'], $upload_dir . $file_evidence);
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('
                INSERT INTO ip_creations (
                    project_id, researcher_id, name_th, name_en, abstract_details, 
                    completion_date, keywords, ip_type, legal_status, registration_number, 
                    registration_agency, approval_date, commercial_status, economic_value, 
                    impact_description, file_submission, file_certificate, file_evidence,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ');
            
            $stmt->execute([
                $project_id, authUser()['id'], $name_th, $name_en, $abstract_details,
                $completion_date, $keywords, $ip_type, $legal_status, $registration_number,
                $registration_agency, $approval_date, $commercial_status, $economic_value,
                $impact_description, $file_submission, $file_certificate, $file_evidence
            ]);
            
            $ip_id = $pdo->lastInsertId();

            if (!empty($inventors_names)) {
                $inventorStmt = $pdo->prepare('INSERT INTO ip_inventors (ip_creation_id, name, proportion_percent) VALUES (?, ?, ?)');
                foreach ($inventors_names as $index => $name) {
                    if (!empty(trim($name))) {
                        $proportion = !empty($inventors_proportions[$index]) ? (int)$inventors_proportions[$index] : 0;
                        $inventorStmt->execute([$ip_id, trim($name), $proportion]);
                    }
                }
            }
            
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Error: " . $e->getMessage());
        }

        redirect('/projects/' . $project_id);
    }
}
