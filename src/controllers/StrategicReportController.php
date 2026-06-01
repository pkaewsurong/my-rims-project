<?php
// src/controllers/StrategicReportController.php

function strategicReportsAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    
    // Check role, typically research admins or executives need this
    if (!hasAnyRole(['research_admin', 'executive'])) {
        die("Unauthorized");
    }

    $year = $_GET['year'] ?? date('Y') + 543;

    // Fetch existing generated reports
    $stmt = $pdo->prepare("
        SELECT r.*, u.name as generator_name 
        FROM strategic_reports r
        LEFT JOIN users u ON r.generated_by = u.id
        WHERE r.fiscal_year = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$year]);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require __DIR__ . '/../../views/reports/strategic.php';
}

function generateReportAction($pdo) {
    if (!isLoggedIn() || !hasAnyRole(['research_admin', 'executive'])) {
        die("Unauthorized");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $year = $_POST['year'] ?? date('Y') + 543;
        $report_type = $_POST['report_type'] ?? 'Custom';
        $report_title = $_POST['title'] ?? "รายงานตัวชี้วัด $report_type ประจำปี $year";

        // MOCK GENERATION PROCESS -> Insert record into DB
        // In a real application, you would generate a PDF or Excel file here and save the path
        try {
            $stmt = $pdo->prepare("
                INSERT INTO strategic_reports (title, report_type, fiscal_year, generated_by, status, created_at) 
                VALUES (?, ?, ?, ?, 'Generated', NOW())
            ");
            $stmt->execute([
                $report_title,
                $report_type,
                $year,
                $_SESSION['user_id']
            ]);
            
            $_SESSION['flash_success'] = "สร้างรายงาน $report_type สำเร็จแล้ว";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "เกิดข้อผิดพลาดในการสร้างรายงาน: " . $e->getMessage();
        }
        
        redirect('/strategic-reports');
    }
}
