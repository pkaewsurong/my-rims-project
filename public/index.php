<?php
// public/index.php - New lightweight router for Vercel pointing to the new main/ directory

$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri);
$path = $parsed_url['path'] ?? '/';

// Clean the path (remove double slashes, trailing slashes if any)
$path = preg_replace('#/+#', '/', $path);

// If root, route to main/login.php
if ($path === '/' || $path === '') {
    $target = __DIR__ . '/../main/login.php';
    $_SERVER['SCRIPT_FILENAME'] = realpath($target);
    $_SERVER['PHP_SELF'] = '/main/login.php';
    chdir(dirname($target));
    require 'login.php';
    exit;
}

// Check if request is pointing to a file under main/
if (strpos($path, '/main/') === 0) {
    $relative_path = substr($path, 6); // Strip "/main/"
    $target = __DIR__ . '/../main/' . $relative_path;
    
    if (file_exists($target) && !is_dir($target)) {
        // If it's a PHP file, execute it
        if (pathinfo($target, PATHINFO_EXTENSION) === 'php') {
            $_SERVER['SCRIPT_FILENAME'] = realpath($target);
            $_SERVER['PHP_SELF'] = $path;
            chdir(dirname($target));
            require basename($target);
            exit;
        } else {
            // Serve static files (just in case they fall through to PHP)
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
}

// Check if request is pointing to a file under public/
if (strpos($path, '/public/') === 0) {
    $relative_path = substr($path, 8); // Strip "/public/"
    $target = __DIR__ . '/' . $relative_path;
    
    if (file_exists($target) && !is_dir($target)) {
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

// Fallback to 404
http_response_code(404);
echo "404 Not Found";
exit;
