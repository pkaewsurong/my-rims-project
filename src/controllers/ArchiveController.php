<?php
// src/controllers/ArchiveController.php

function indexAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $proposal_id = $_GET['project_id'] ?? null;
    if (!$proposal_id) {
        die("Project ID is required");
    }

    $stmt = $pdo->prepare('SELECT p.* FROM proposals p WHERE p.id = ?');
    $stmt->execute([$proposal_id]);
    $project = $stmt->fetch();

    if (!$project) {
        die("Project not found");
    }

    // Verify ownership
    if ($project['user_id'] != authUser()['id']) {
        die("Unauthorized");
    }

    // Fetch archives
    $stmt = $pdo->prepare('SELECT * FROM data_archives WHERE proposal_id = ? ORDER BY created_at DESC');
    $stmt->execute([$proposal_id]);
    $archives = $stmt->fetchAll();

    // Fetch settings
    $stmt = $pdo->prepare('SELECT * FROM data_archive_settings WHERE proposal_id = ?');
    $stmt->execute([$proposal_id]);
    $settings = $stmt->fetch();

    require __DIR__ . '/../../views/archives/index.php';
}

function storeAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $proposal_id = $_POST['proposal_id'] ?? null;
        $dataset_name = $_POST['dataset_name'] ?? '';
        $data_type = $_POST['data_type'] ?? '';
        $description = $_POST['description'] ?? '';
        $access_level = $_POST['access_level'] ?? 'private';

        if (!$proposal_id || empty($dataset_name)) {
            die("Invalid data");
        }
        
        // Handle File Upload
        $upload_dir = __DIR__ . '/../../public/uploads/archives/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0777, true);
        }

        $file_path = null;
        $file_size = 0;
        if (!empty($_FILES['file_path']['name'])) {
            $file_path = time() . '_archive_' . basename($_FILES['file_path']['name']);
            $file_size = $_FILES['file_path']['size'];
            @move_uploaded_file($_FILES['file_path']['tmp_name'], $upload_dir . $file_path);
        }

        $stmt = $pdo->prepare('
            INSERT INTO data_archives (
                proposal_id, dataset_name, data_type, description, 
                file_path, file_size, access_level, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ');
        
        $stmt->execute([
            $proposal_id, $dataset_name, $data_type, $description,
            $file_path, $file_size, $access_level
        ]);

        redirect('/archives?project_id=' . $proposal_id);
    }
}

function settingsStoreAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $proposal_id = $_POST['proposal_id'] ?? null;
        $retention_period = !empty($_POST['retention_period']) ? (int)$_POST['retention_period'] : 5;
        $destruction_method = $_POST['destruction_method'] ?? 'Delete physically & digitally';

        if (!$proposal_id) {
            die("Invalid data");
        }

        $stmtCheck = $pdo->prepare('SELECT id FROM data_archive_settings WHERE proposal_id = ?');
        $stmtCheck->execute([$proposal_id]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            $stmt = $pdo->prepare('UPDATE data_archive_settings SET retention_period = ?, destruction_method = ?, updated_at = NOW() WHERE proposal_id = ?');
            $stmt->execute([$retention_period, $destruction_method, $proposal_id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO data_archive_settings (proposal_id, retention_period, destruction_method, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
            $stmt->execute([$proposal_id, $retention_period, $destruction_method]);
        }

        redirect('/archives?project_id=' . $proposal_id);
    }
}
