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

function forgotPasswordAction($pdo) {
    if (isLoggedIn()) {
        redirect('/');
    }

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $error = 'กรุณากรอกอีเมล (Email is required)';
        } else {
            // Check if email exists
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate secure random token
                $token = bin2hex(random_bytes(32));

                // Delete old tokens for this email
                $deleteStmt = $pdo->prepare('DELETE FROM password_reset_tokens WHERE email = ?');
                $deleteStmt->execute([$email]);

                // Insert new token
                $insertStmt = $pdo->prepare('INSERT INTO password_reset_tokens (email, token, created_at) VALUES (?, ?, NOW())');
                $insertStmt->execute([$email, $token]);

                // Build absolute reset link
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'];
                $resetLink = $protocol . '://' . $host . url('/reset-password?token=' . $token . '&email=' . urlencode($email));

                // Send email (and log it)
                $emailSent = sendPasswordResetEmail($email, $resetLink);

                if ($emailSent) {
                    $success = 'ส่งลิงก์สำหรับตั้งค่ารหัสผ่านใหม่ไปยังอีเมลของท่านเรียบร้อยแล้ว กรุณาตรวจสอบกล่องจดหมายของท่าน (รวมถึงโฟลเดอร์สแปม)';
                } else {
                    $success = 'ระบบได้สร้างลิงก์สำหรับตั้งค่ารหัสผ่านใหม่แล้ว แต่ไม่สามารถส่งอีเมลได้ (SMTP ยังไม่ได้ตั้งค่า) — สามารถดูลิงก์ได้ในไฟล์ public/uploads/email_logs.txt';
                }
            } else {
                $error = 'ไม่พบบัญชีผู้ใช้งานที่ใช้อีเมลนี้ในระบบ (Email not found in our records)';
            }
        }
    }

    require __DIR__ . '/../../views/auth/forgot-password.php';
}

function resetPasswordAction($pdo) {
    if (isLoggedIn()) {
        redirect('/');
    }

    $error = '';
    $success = '';

    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    $email = $_GET['email'] ?? $_POST['email'] ?? '';

    if (empty($token) || empty($email)) {
        $_SESSION['alert_msg'] = 'ลิงก์ไม่ถูกต้องหรือไม่มีข้อมูลโทเคน';
        redirect('/forgot-password');
    }

    // Verify token is valid and not expired (e.g. within 1 hour)
    $stmt = $pdo->prepare('SELECT * FROM password_reset_tokens WHERE email = ? AND token = ? LIMIT 1');
    $stmt->execute([$email, $token]);
    $resetToken = $stmt->fetch();

    if (!$resetToken) {
        $_SESSION['alert_msg'] = 'ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้อง หรือได้ใช้งานไปแล้ว';
        redirect('/forgot-password');
    }

    // Validate expiration (1 hour = 3600 seconds)
    $createdAt = strtotime($resetToken['created_at']);
    if (time() - $createdAt > 3600) {
        // Delete expired token
        $deleteStmt = $pdo->prepare('DELETE FROM password_reset_tokens WHERE email = ?');
        $deleteStmt->execute([$email]);

        $_SESSION['alert_msg'] = 'ลิงก์รีเซ็ตรหัสผ่านนี้หมดอายุแล้ว (อายุการใช้งาน 1 ชั่วโมง)';
        redirect('/forgot-password');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        $password_confirmation = $_POST['password_confirmation'] ?? '';

        if (empty($password)) {
            $error = 'กรุณากรอกรหัสผ่านใหม่';
        } elseif (strlen($password) < 6) {
            $error = 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
        } elseif ($password !== $password_confirmation) {
            $error = 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน';
        } else {
            // Update password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare('UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?');
            $updateStmt->execute([$hashedPassword, $email]);

            // Delete token after successful reset
            $deleteStmt = $pdo->prepare('DELETE FROM password_reset_tokens WHERE email = ?');
            $deleteStmt->execute([$email]);

            $_SESSION['alert_msg'] = 'เปลี่ยนรหัสผ่านใหม่เรียบร้อยแล้ว กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่';
            redirect('/login');
        }
    }

    require __DIR__ . '/../../views/auth/reset-password.php';
}

