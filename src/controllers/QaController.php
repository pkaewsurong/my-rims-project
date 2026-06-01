<?php
// src/controllers/QaController.php

function indexAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    requireRole('admin');

    // Fetch proposals ready for QA or already QA'd (e.g. status completed/closed, or specifically for QA)
    // For this example, let's fetch all proposals that have final reports submitted
    $stmt = $pdo->prepare('
        SELECT p.*, u.name as researcher_name, fr.status as report_status, fr.id as report_id, fr.submission_date 
        FROM proposals p 
        JOIN users u ON p.user_id = u.id 
        JOIN final_reports fr ON p.id = fr.proposal_id 
        ORDER BY fr.submission_date DESC
    ');
    $stmt->execute();
    $reports = $stmt->fetchAll();

    require __DIR__ . '/../../views/qa/index.php';
}

function exportAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    requireRole('admin');
    
    $format = $_GET['format'] ?? 'csv';
    $year = $_GET['year'] ?? date('Y');
    
    // Fetch data for export
    $stmt = $pdo->prepare('
        SELECT p.code, p.title, u.name as researcher_name, p.status, p.budget_total,
               (SELECT COUNT(*) FROM publications pub WHERE pub.proposal_id = p.id) as pub_count,
               (SELECT COUNT(*) FROM ip_assets ip WHERE ip.proposal_id = p.id) as ip_count
        FROM proposals p
        JOIN users u ON p.user_id = u.id
        WHERE YEAR(p.created_at) = ?
    ');
    $stmt->execute([$year]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=rims_export_' . $year . '.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('Project Code', 'Title', 'Researcher', 'Status', 'Budget', 'Publications', 'IP Assets'));
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    } else {
        // Handle other formats like Excel/PDF if needed
        die("Format not supported yet.");
    }
}
