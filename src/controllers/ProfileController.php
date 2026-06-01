<?php
// src/controllers/ProfileController.php

function indexAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $user_id = authUser()['id'];

    // Fetch full user details along with primary role
    $stmt = $pdo->prepare("
        SELECT u.*, r.name as role_name 
        FROM users u 
        LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
        LEFT JOIN roles r ON mhr.role_id = r.id 
        WHERE u.id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch metrics if available
    $stmtMetrics = $pdo->prepare("SELECT * FROM metric_snapshots WHERE user_id = ? ORDER BY fiscal_year DESC LIMIT 1");
    $stmtMetrics->execute([$user_id]);
    $metrics = $stmtMetrics->fetch(PDO::FETCH_ASSOC);

    require __DIR__ . '/../../views/profile/index.php';
}

function editAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $user_id = authUser()['id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    require __DIR__ . '/../../views/profile/edit.php';
}

function updateAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = authUser()['id'];
        $prefix = trim($_POST['prefix'] ?? '');
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($first_name) || empty($last_name) || empty($email)) {
            $_SESSION['alert_msg'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
            redirect('/profile/edit');
        }
        
        // Check if email already exists for another user
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmtCheck->execute([$email, $user_id]);
        if ($stmtCheck->fetchColumn()) {
            $_SESSION['alert_msg'] = 'อีเมลนี้ถูกใช้งานแล้วในระบบ';
            redirect('/profile/edit');
        }

        $fullName = trim($prefix . ' ' . $first_name . ' ' . $last_name);

        if (!empty($password)) {
            // Update with password
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET prefix = ?, first_name = ?, last_name = ?, name = ?, email = ?, password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$prefix, $first_name, $last_name, $fullName, $email, $hashed, $user_id]);
        } else {
            // Update without password
            $stmt = $pdo->prepare("UPDATE users SET prefix = ?, first_name = ?, last_name = ?, name = ?, email = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$prefix, $first_name, $last_name, $fullName, $email, $user_id]);
        }

        // Update session name if changed
        $_SESSION['user_name'] = $fullName;

        $_SESSION['alert_msg'] = 'บันทึกข้อมูลส่วนตัวเรียบร้อยแล้ว';
        redirect('/profile');
    }
}
