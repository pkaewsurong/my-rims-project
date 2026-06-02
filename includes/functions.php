<?php
// includes/functions.php

/**
 * Escape HTML entities to prevent XSS.
 * Equivalent to Laravel's e() or {{ }}
 */
function e($value) {
    if (is_null($value)) {
        return '';
    }
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
}

/**
 * Check if a user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get authenticated user data from session
 */
function authUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? 'User'
        ];
    }
    return null;
}

/**
 * Simple redirect helper
 */
function redirect($path) {
    $is_vercel = getenv('VERCEL') !== false || isset($_SERVER['VERCEL']);
    if ($is_vercel) {
        header("Location: {$path}");
    } else {
        $base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($base_path === '/' || $base_path === '\\') {
            $base_path = '';
        }
        header("Location: {$base_path}{$path}");
    }
    exit();
}

/**
 * Check if the authenticated user has a specific role
 */
function hasRole($role) {
    if (!isLoggedIn()) return false;
    $roles = $_SESSION['user_roles'] ?? [];
    // Allow System Administrator and admin to bypass role checks
    foreach ($roles as $r) {
        if (strcasecmp($r, 'System Administrator') === 0 || strcasecmp($r, 'admin') === 0) return true;
        if (strcasecmp($r, $role) === 0) return true;
    }
    return false;
}

/**
 * Check if the authenticated user has any of the given roles
 */
function hasAnyRole(array $rolesArray) {
    if (!isLoggedIn()) return false;
    $userRoles = $_SESSION['user_roles'] ?? [];
    
    // Allow System Administrator and admin to bypass role checks
    foreach ($userRoles as $uRole) {
        if (strcasecmp($uRole, 'System Administrator') === 0 || strcasecmp($uRole, 'admin') === 0) return true;
    }

    foreach ($rolesArray as $reqRole) {
        foreach ($userRoles as $uRole) {
            if (strcasecmp($reqRole, $uRole) === 0) return true;
        }
    }
    return false;
}

/**
 * Require a specific role to access the current page. Redirects to a 403 page or home if unauthorized.
 */
function requireRole($role) {
    if (!hasRole($role)) {
        http_response_code(403);
        $rolesStr = isset($_SESSION['user_roles']) ? implode(', ', $_SESSION['user_roles']) : 'None';
        $userIdStr = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not Logged In';
        die("403 Forbidden: You do not have the required role ('$role') to access this page. userId: $userIdStr, roles: $rolesStr");
    }
}

/**
 * Require any of the listed roles to access the current page.
 */
function requireAnyRole(array $roles) {
    if (!hasAnyRole($roles)) {
        http_response_code(403);
        $reqRolesStr = implode(', ', $roles);
        $rolesStr = isset($_SESSION['user_roles']) ? implode(', ', $_SESSION['user_roles']) : 'None';
        $userIdStr = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not Logged In';
        die("403 Forbidden: You do not have the required role to access this page. Required: [$reqRolesStr], userId: $userIdStr, roles: $rolesStr");
    }
}

/**
 * Add a new notification for a specific user.
 */
function addNotification($pdo, $user_id, $title, $message) {
    $stmt = $pdo->prepare('INSERT INTO notifications (user_id, title, message, created_at) VALUES (?, ?, ?, NOW())');
    return $stmt->execute([$user_id, $title, $message]);
}

/**
 * Get unread notification count for the current user.
 * NOTE: Use getRecentNotifications() first and pass the result here to avoid double querying.
 */
function getUnreadNotificationCount($pdo, array $notifications = null) {
    if (!isLoggedIn()) return 0;
    // If notifications already fetched, derive count from them (avoids extra DB query)
    if ($notifications !== null) {
        return count(array_filter($notifications, fn($n) => !$n['is_read']));
    }
    $user_id = authUser()['id'];
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

/**
 * Generate notifications for the current user (limit to 10).
 */
function getRecentNotifications($pdo) {
    if (!isLoggedIn()) return [];
    $user_id = authUser()['id'];
    $stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10');
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Generate environment-aware URL path.
 */
function url($path) {
    $is_vercel = getenv('VERCEL') !== false || isset($_SERVER['VERCEL']);
    if ($is_vercel) {
        return $path;
    }
    
    $base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if ($base_path === '/' || $base_path === '\\') {
        $base_path = '/public';
    }
    return $base_path . $path;
}

/**
 * Send password reset email using PHPMailer with Gmail SMTP.
 * Falls back to logging the reset link if SMTP is not configured.
 */
function sendPasswordResetEmail($email, $resetLink) {
    // Load Composer autoloader for PHPMailer
    require_once dirname(__DIR__) . '/vendor/autoload.php';

    // Load mail configuration
    $mailConfig = require dirname(__DIR__) . '/config/mail.php';

    $subject = "รีเซ็ตรหัสผ่านสำหรับระบบ RIMS";
    
    // Create an elegant, responsive HTML email body matching the earth-tone design system
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: "Sarabun", "Inter", sans-serif; background-color: #FAF7F2; color: #4a3728; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #E6DCD2; border-radius: 16px; overflow: hidden; margin-top: 40px; box-shadow: 0 4px 12px rgba(139, 94, 60, 0.05); }
            .header { background-color: #8B5E3C; padding: 30px; text-align: center; }
            .header h1 { color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px; }
            .content { padding: 40px 30px; line-height: 1.6; }
            .content h2 { color: #3d2f24; margin-top: 0; font-size: 20px; }
            .content p { font-size: 15px; color: #574639; }
            .btn-container { text-align: center; margin: 30px 0; }
            .btn { display: inline-block; background-color: #8B5E3C; color: #ffffff !important; text-decoration: none; padding: 14px 30px; border-radius: 12px; font-weight: bold; font-size: 15px; transition: background-color 0.2s; box-shadow: 0 4px 12px rgba(139, 94, 60, 0.2); }
            .btn:hover { background-color: #704829; }
            .footer { background-color: #FAF6F0; padding: 20px; text-align: center; border-top: 1px solid #E6DCD2; font-size: 12px; color: #8a7667; }
            .warning { font-size: 13px; color: #b45309; background-color: #fef3c7; border: 1px solid #fde68a; padding: 12px; border-radius: 8px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>RIMS</h1>
            </div>
            <div class="content">
                <h2>สวัสดีครับ/ค่ะ</h2>
                <p>ท่านได้ส่งคำขอเพื่อรีเซ็ตรหัสผ่านสำหรับเข้าใช้งานระบบบริหารงานวิจัยและนวัตกรรม (RIMS)</p>
                <p>กรุณาคลิกปุ่มด้านล่างนี้เพื่อตั้งรหัสผ่านใหม่:</p>
                <div class="btn-container">
                    <a href="' . e($resetLink) . '" class="btn" target="_blank">ตั้งรหัสผ่านใหม่ (Reset Password)</a>
                </div>
                <div class="warning">
                    <strong>คำเตือน:</strong> ลิงก์สำหรับรีเซ็ตรหัสผ่านนี้จะมีอายุการใช้งาน 1 ชั่วโมงเพื่อความปลอดภัย หากท่านไม่ได้ส่งคำขอนี้ กรุณาเพิกเฉยต่ออีเมลฉบับนี้
                </div>
                <p style="margin-top: 30px;">ขอแสดงความนับถือ,<br>ทีมผู้ดูแลระบบ RIMS</p>
            </div>
            <div class="footer">
                &copy; ' . date('Y') . ' Research & Innovation Management System. สงวนลิขสิทธิ์.
            </div>
        </div>
    </html>
    ';

    $emailSent = false;
    $emailError = '';

    // Only attempt SMTP if credentials are configured
    if (!empty($mailConfig['username']) && !empty($mailConfig['password'])) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = $mailConfig['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $mailConfig['username'];
            $mail->Password   = $mailConfig['password'];
            $mail->SMTPSecure = $mailConfig['encryption'];
            $mail->Port       = (int) $mailConfig['port'];
            $mail->CharSet    = 'UTF-8';

            // Sender & Recipient
            $fromEmail = $mailConfig['from_email'] ?: $mailConfig['username'];
            $mail->setFrom($fromEmail, $mailConfig['from_name']);
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = "สวัสดีครับ/ค่ะ\nท่านได้ส่งคำขอเพื่อรีเซ็ตรหัสผ่านสำหรับเข้าใช้งานระบบ RIMS\nคลิกลิงก์นี้เพื่อตั้งรหัสผ่านใหม่: " . $resetLink . "\n(ลิงก์จะมีอายุการใช้งาน 1 ชั่วโมง)";

            $mail->send();
            $emailSent = true;
        } catch (PHPMailer\PHPMailer\Exception $e) {
            $emailError = $mail->ErrorInfo;
        }
    } else {
        $emailError = 'SMTP credentials not configured in config/mail.php';
    }
    
    // Always log the reset email to public/uploads/email_logs.txt for debugging
    $log_dir = dirname(__DIR__) . '/public/uploads';
    if (!file_exists($log_dir)) {
        @mkdir($log_dir, 0777, true);
    }
    $log_file = $log_dir . '/email_logs.txt';
    
    $log_content = "==================================================\n";
    $log_content .= "Date: " . date('Y-m-d H:i:s') . "\n";
    $log_content .= "To: " . $email . "\n";
    $log_content .= "Subject: " . $subject . "\n";
    $log_content .= "Reset Link: " . $resetLink . "\n";
    $log_content .= "SMTP Status: " . ($emailSent ? "✅ SENT SUCCESSFULLY" : "❌ FAILED - " . $emailError) . "\n";
    $log_content .= "--------------------------------------------------\n";
    $log_content .= "HTML Body (Text Version):\n";
    $log_content .= "  สวัสดีครับ/ค่ะ ท่านได้ส่งคำขอเพื่อรีเซ็ตรหัสผ่านสำหรับเข้าใช้งานระบบ RIMS\n";
    $log_content .= "  สามารถคลิกที่ลิงก์นี้เพื่อตั้งรหัสผ่านใหม่: " . $resetLink . "\n";
    $log_content .= "  (ลิงก์จะมีอายุการใช้งาน 1 ชั่วโมง)\n";
    $log_content .= "==================================================\n\n";
    @file_put_contents($log_file, $log_content, FILE_APPEND);
    
    return $emailSent;
}


