<?php
// Include database first to establish connection for session handler
require_once __DIR__ . '/../config/database.php';

// Use database sessions on Vercel (serverless has no persistent filesystem)
$is_vercel = getenv('VERCEL') !== false || isset($_SERVER['VERCEL']);
if ($is_vercel && isset($pdo)) {
    require_once __DIR__ . '/../includes/DatabaseSessionHandler.php';
    $handler = new DatabaseSessionHandler($pdo);
    session_set_save_handler($handler, true);
}

$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri);
$path = $parsed_url['path'] ?? '/';

// Clean the path (remove double slashes, trailing slashes if any)
$path = preg_replace('#/+#', '/', $path);

// Normalize path: If it starts with /main/, strip it so it resolves the same
$relative_path = $path;
if (strpos($relative_path, '/main/') === 0) {
    $relative_path = '/' . substr($relative_path, 6);
}

// If root, route to login.php
if ($relative_path === '/' || $relative_path === '') {
    $relative_path = '/login.php';
}

// 1. Try to find the file under main/
$target = __DIR__ . '/../main' . $relative_path;

if (file_exists($target) && !is_dir($target)) {
    // If it's a PHP file, execute it
    if (pathinfo($target, PATHINFO_EXTENSION) === 'php') {
        $_SERVER['SCRIPT_FILENAME'] = realpath($target);
        $_SERVER['PHP_SELF'] = $relative_path;
        chdir(dirname($target));
        require basename($target);
        exit;
    } else {
        // Serve static files (fallback if not handled by vercel.json)
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'pdf' => 'application/pdf',
            'json' => 'application/json'
        ];
        $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
        $content_type = $mime_types[$ext] ?? 'application/octet-stream';
        header('Content-Type: ' . $content_type);
        readfile($target);
        exit;
    }
}

// 2. Try to find the file under public/ (for uploads/ etc.)
$target_public = __DIR__ . $relative_path;
if (file_exists($target_public) && !is_dir($target_public)) {
    $mime_types = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'pdf' => 'application/pdf',
        'json' => 'application/json'
    ];
    $ext = strtolower(pathinfo($target_public, PATHINFO_EXTENSION));
    $content_type = $mime_types[$ext] ?? 'application/octet-stream';
    header('Content-Type: ' . $content_type);
    readfile($target_public);
    exit;
}

// Fallback to 404
http_response_code(404);
echo "404 Not Found";
exit;
