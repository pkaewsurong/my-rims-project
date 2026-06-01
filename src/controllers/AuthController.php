<?php
// src/controllers/AuthController.php

function loginAction($pdo) {
    if (isLoggedIn()) {
        redirect('/');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['alert_msg'] = 'กรุณากรอกอีเมลและรหัสผ่าน (Email and password are required)';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    
                    // Fetch Roles
                    $roleStmt = $pdo->prepare('
                        SELECT r.name 
                        FROM roles r
                        JOIN model_has_roles mhr ON r.id = mhr.role_id
                        WHERE mhr.model_id = ? AND mhr.model_type = "App\\\\Models\\\\User"
                    ');
                    $roleStmt->execute([$user['id']]);
                    $_SESSION['user_roles'] = $roleStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
                    
                    redirect('/');
                } else {
                    $_SESSION['alert_msg'] = 'รหัสผ่านผิดพลาด';
                }
            } else {
                $_SESSION['alert_msg'] = 'ไม่พบบัญชีผู้ใช้งานนี้';
            }
        }
    }

    require __DIR__ . '/../../views/auth/login.php';
}

function registerAction($pdo) {
    if (isLoggedIn()) {
        redirect('/');
    }

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $prefix = $_POST['prefix'] ?? '';
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirmation = $_POST['password_confirmation'] ?? '';

        if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
            $error = 'กรุณากรอกข้อมูลให้ครบถ้วน (All fields are required)';
        } elseif ($password !== $password_confirmation) {
            $error = 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน (Passwords do not match)';
        } elseif (strlen($password) < 6) {
            $error = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร (Password must be at least 6 characters)';
        } else {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'อีเมลนี้ถูกใช้งานแล้ว (Email already exists)';
            } else {
                $fullName = trim($prefix . ' ' . $first_name . ' ' . $last_name);
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (prefix, first_name, last_name, name, email, password) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$prefix, $first_name, $last_name, $fullName, $email, $hashedPassword]);
                $newUserId = $pdo->lastInsertId();

                // Automatically assign 'Researcher' role to new users
                $roleName = 'Researcher';
                $roleStmt = $pdo->prepare('SELECT id FROM roles WHERE name = ? LIMIT 1');
                $roleStmt->execute([$roleName]);
                $roleId = $roleStmt->fetchColumn();

                // If the role doesn't exist for some reason, create it
                if (!$roleId) {
                    $createRoleStmt = $pdo->prepare('INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES (?, "web", NOW(), NOW())');
                    $createRoleStmt->execute([$roleName]);
                    $roleId = $pdo->lastInsertId();
                }

                if ($roleId) {
                    $assignStmt = $pdo->prepare('INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, "App\\\\Models\\\\User", ?)');
                    $assignStmt->execute([$roleId, $newUserId]);
                }

                $success = 'ลงทะเบียนสำเร็จ กรุณาเข้าสู่ระบบ (Registration successful, please login)';
            }
        }
    }

    require __DIR__ . '/../../views/auth/register.php';
}

function logoutAction() {
    session_destroy();
    redirect('/login');
}
