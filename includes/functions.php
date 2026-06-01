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
 */
function getUnreadNotificationCount($pdo) {
    if (!isLoggedIn()) return 0;
    $user_id = authUser()['id'];
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

/**
 * Get notifications for the current user (limit to 10).
 */
function getRecentNotifications($pdo) {
    if (!isLoggedIn()) return [];
    $user_id = authUser()['id'];
    $stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10');
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

