<?php
// src/controllers/FileController.php

function fileStoreAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project_id = $_POST['project_id'] ?? null;
        $file_type = $_POST['file_type'] ?? '';

        if (!$project_id || empty($file_type) || !isset($_FILES['file_path']) || $_FILES['file_path']['error'] !== UPLOAD_ERR_OK) {
            die("Invalid file upload");
        }

        $upload_dir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0755, true);
        }

        // Generate unique filename
        $file_info = pathinfo($_FILES['file_path']['name']);
        $new_filename = uniqid() . '.' . $file_info['extension'];
        $destination = $upload_dir . $new_filename;

        if (@move_uploaded_file($_FILES['file_path']['tmp_name'], $destination)) {
            // Save relative path to DB
            $db_path = 'uploads/' . $new_filename;

            $stmt = $pdo->prepare('
                INSERT INTO project_files (project_id, file_path, file_type, upload_date) 
                VALUES (?, ?, ?, NOW())
            ');
            $stmt->execute([$project_id, $db_path, $file_type]);
        }

        redirect('/projects/' . $project_id);
    }
}

function fileDestroyAction($pdo, $file_id) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Find file
        $stmt = $pdo->prepare('SELECT * FROM project_files WHERE id = ?');
        $stmt->execute([$file_id]);
        $file = $stmt->fetch();

        if ($file) {
            $project_id = $file['project_id'];
            
            // Delete physical file
            $physical_path = __DIR__ . '/../../public/' . $file['file_path'];
            if (file_exists($physical_path)) {
                unlink($physical_path);
            }

            // Delete from DB
            $stmt = $pdo->prepare('DELETE FROM project_files WHERE id = ?');
            $stmt->execute([$file_id]);

            redirect('/projects/' . $project_id);
        }
    }
}
