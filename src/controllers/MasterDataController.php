<?php
// src/controllers/MasterDataController.php

function masterDataAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    
    // Check role, only Research Admins should manage master data
    if (!hasRole('research_admin')) {
        die("Unauthorized");
    }

    $tab = $_GET['tab'] ?? 'funders';

    // Fetch data based on active tab
    $funders = [];
    $journals = [];
    $tiers = [];

    if ($tab === 'funders') {
        $stmt = $pdo->query("SELECT * FROM funders ORDER BY name ASC");
        $funders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($tab === 'journals') {
        $stmt = $pdo->query("SELECT * FROM journals ORDER BY name ASC");
        $journals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($tab === 'tiers') {
        $stmt = $pdo->query("SELECT * FROM metric_tiers ORDER BY category ASC, points DESC");
        $tiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    require __DIR__ . '/../../views/admin/master_data.php';
}

function masterDataStoreAction($pdo) {
    if (!isLoggedIn() || !hasRole('research_admin')) {
        die("Unauthorized");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $type = $_POST['type'] ?? '';

        try {
            if ($type === 'funder') {
                $stmt = $pdo->prepare("INSERT INTO funders (name, type, status) VALUES (?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['funder_type'],
                    $_POST['status']
                ]);
                $_SESSION['flash_success'] = "เพิ่มแหล่งทุนสำเร็จ";
                redirect('/admin/master-data?tab=funders');
            } 
            elseif ($type === 'journal') {
                $stmt = $pdo->prepare("INSERT INTO journals (name, issn, quartile, database_index) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['issn'],
                    $_POST['quartile'],
                    $_POST['database_index']
                ]);
                $_SESSION['flash_success'] = "เพิ่มฐานวารสารสำเร็จ";
                redirect('/admin/master-data?tab=journals');
            }
            elseif ($type === 'tier') {
                $stmt = $pdo->prepare("INSERT INTO metric_tiers (category, level_name, description, points) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['category'],
                    $_POST['level_name'],
                    $_POST['description'],
                    $_POST['points']
                ]);
                $_SESSION['flash_success'] = "เพิ่มเกณฑ์การให้คะแนนสำเร็จ";
                redirect('/admin/master-data?tab=tiers');
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            redirect('/admin/master-data?tab=' . ($type === 'funder' ? 'funders' : ($type === 'journal' ? 'journals' : 'tiers')));
        }
    }
}
