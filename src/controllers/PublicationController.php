<?php
// src/controllers/PublicationController.php

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

    require __DIR__ . '/../../views/publications/create.php';
}

function storeAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project_id = $_POST['project_id'] ?? null;
        $title_th = $_POST['title_th'] ?? '';
        $title_en = $_POST['title_en'] ?? '';
        $publication_type = $_POST['publication_type'] ?? '';
        $publish_year = $_POST['publish_year'] ?? date('Y');
        $journal_name = $_POST['journal_name'] ?? '';
        $issn = $_POST['issn'] ?? '';
        $volume = $_POST['volume'] ?? '';
        $issue = $_POST['issue'] ?? '';
        $page_length = $_POST['page_length'] ?? '';
        $indexing_database = $_POST['indexing_database'] ?? '';
        $quartile = $_POST['quartile'] ?? '';
        $journal_level = $_POST['journal_level'] ?? '';
        $impact_factor = $_POST['impact_factor'] ?? '';
        $status = $_POST['status'] ?? 'published';
        $doi_url = $_POST['doi_url'] ?? '';
        $utilization_summary = $_POST['utilization_summary'] ?? '';
        
        $authors_names = $_POST['author_name'] ?? [];
        $authors_roles = $_POST['author_role'] ?? [];

        if (!$project_id || empty($title_th) || empty($journal_name)) {
            die("Invalid data: Title (TH) and Journal Name are required.");
        }
        
        // Handle File Uploads
        $upload_dir = __DIR__ . '/../../public/uploads/publications/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_full_text = null;
        if (!empty($_FILES['file_full_text']['name'])) {
            $file_full_text = time() . '_full_' . basename($_FILES['file_full_text']['name']);
            move_uploaded_file($_FILES['file_full_text']['tmp_name'], $upload_dir . $file_full_text);
        }

        $file_acceptance = null;
        if (!empty($_FILES['file_acceptance']['name'])) {
            $file_acceptance = time() . '_acc_' . basename($_FILES['file_acceptance']['name']);
            move_uploaded_file($_FILES['file_acceptance']['tmp_name'], $upload_dir . $file_acceptance);
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('
                INSERT INTO publications (
                    project_id, researcher_id, title_th, title_en, publication_type, 
                    publish_year, journal_name, issn, volume, issue, 
                    page_length, indexing_database, quartile, journal_level, impact_factor,
                    status, doi_url, utilization_summary, file_full_text, file_acceptance,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ');
            
            $stmt->execute([
                $project_id, authUser()['id'], $title_th, $title_en, $publication_type,
                $publish_year, $journal_name, $issn, $volume, $issue,
                $page_length, $indexing_database, $quartile, $journal_level, $impact_factor,
                $status, $doi_url, $utilization_summary, $file_full_text, $file_acceptance
            ]);
            
            $publication_id = $pdo->lastInsertId();

            if (!empty($authors_names)) {
                $authorStmt = $pdo->prepare('INSERT INTO publication_authors (publication_id, name, role) VALUES (?, ?, ?)');
                foreach ($authors_names as $index => $name) {
                    if (!empty(trim($name))) {
                        $role = $authors_roles[$index] ?? 'Co-Author';
                        $authorStmt->execute([$publication_id, trim($name), $role]);
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
