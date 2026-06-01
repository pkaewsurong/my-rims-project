<?php
// src/controllers/BudgetController.php

function manageAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    requireAnyRole(['Researcher', 'Research Admin']);

    $proposal_id = $_GET['id'] ?? null;
    if (!$proposal_id) {
        die("Proposal ID is required");
    }

    $user_id = authUser()['id'];

    // Verify ownership or reviewer status
    $stmt = $pdo->prepare('SELECT * FROM proposals WHERE id = ?');
    $stmt->execute([$proposal_id]);
    $proposal = $stmt->fetch();

    if (!$proposal) {
        die("Proposal not found");
    }

    if (strtolower($proposal['status']) !== 'approved') {
        $_SESSION['alert_msg'] = 'โครงการยังอยู่ในสถานะ ' . $proposal['status'] . ' ยังไม่สามารถเข้าไปจัดการงบประมาณได้';
        redirect('/proposals');
    }

    $stmtSources = $pdo->prepare('SELECT * FROM proposal_funding_sources WHERE proposal_id = ? ORDER BY created_at ASC');
    $stmtSources->execute([$proposal_id]);
    $funding_sources = $stmtSources->fetchAll();

    // Auto-create initial funding source from proposal if none exists
    if (empty($funding_sources) && $proposal['budget_total'] > 0) {
        $sourceName = 'งบประมาณเริ่มต้นโครงการ';
        if (!empty($proposal['funding_source_id'])) {
            $stmtGetSourceName = $pdo->prepare('SELECT name FROM funding_sources WHERE id = ?');
            $stmtGetSourceName->execute([$proposal['funding_source_id']]);
            $sourceData = $stmtGetSourceName->fetch();
            if ($sourceData) {
                $sourceName = $sourceData['name'];
            }
        }
        
        $sourceType = (strpos($sourceName, 'ภายนอก') !== false) ? 'External' : 'Internal';
        
        $stmtInsertSource = $pdo->prepare('
            INSERT INTO proposal_funding_sources (proposal_id, name, type, amount, created_at, updated_at) 
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ');
        $stmtInsertSource->execute([$proposal_id, $sourceName, $sourceType, $proposal['budget_total']]);
        
        // Refetch
        $stmtSources->execute([$proposal_id]);
        $funding_sources = $stmtSources->fetchAll();
    }

    // Fetch budget items
    $stmtItems = $pdo->prepare('SELECT * FROM proposal_budget_items WHERE proposal_id = ? ORDER BY year ASC, created_at ASC');
    $stmtItems->execute([$proposal_id]);
    $budget_items = $stmtItems->fetchAll();

    // Fetch all available funding sources from master data
    $stmtAllSources = $pdo->query('SELECT * FROM funding_sources');
    $all_funding_sources = $stmtAllSources->fetchAll();

    // Calculate Totals
    $total_sources = array_sum(array_column($funding_sources, 'amount'));
    $total_allocated = array_sum(array_column($budget_items, 'amount'));
    $available_balance = $total_sources - $total_allocated;

    require __DIR__ . '/../../views/projects/budget.php';
}

function addSourceAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    requireAnyRole(['Researcher', 'Research Admin']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $proposal_id = $_POST['proposal_id'] ?? null;
        $name = $_POST['type_display'] ?? $_POST['name'] ?? ''; // Prefer selected funder name
        $type = $_POST['type'] ?? 'External';
        $amount = !empty($_POST['amount']) ? (float)$_POST['amount'] : 0;

        if (!$proposal_id || empty($name) || $amount <= 0) {
            die("Invalid data");
        }

        $stmt = $pdo->prepare('
            INSERT INTO proposal_funding_sources (proposal_id, name, type, amount, created_at, updated_at) 
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ');
        $stmt->execute([$proposal_id, $name, $type, $amount]);

        redirect('/proposals/budget?id=' . $proposal_id);
    }
}

function addLineItemAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    requireAnyRole(['Researcher', 'Research Admin']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $proposal_id = $_POST['proposal_id'] ?? null;
        $year = !empty($_POST['year']) ? (int)$_POST['year'] : date('Y') + 543;
        $category = $_POST['category'] ?? '';
        $description = $_POST['description'] ?? '';
        $amount = !empty($_POST['amount']) ? (float)$_POST['amount'] : 0;

        if (!$proposal_id || empty($category) || empty($description) || $amount <= 0) {
            die("Invalid data");
        }

        $stmt = $pdo->prepare('
            INSERT INTO proposal_budget_items (proposal_id, year, category, description, amount, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ');
        $stmt->execute([$proposal_id, $year, $category, $description, $amount]);

        redirect('/proposals/budget?id=' . $proposal_id);
    }
}
