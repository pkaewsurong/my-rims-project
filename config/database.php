<?php
// config/database.php

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'project_is';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Enable SSL/TLS for remote database hosts (like TiDB Cloud)
$is_local = in_array(strtolower($host), ['127.0.0.1', 'localhost', '::1']);
if (!$is_local) {
    // Scan for system CA certificate bundle paths or fallback to /dev/null as a dummy path
    // to trigger the SSL connection logic.
    // We use integer values (1007 for MYSQL_ATTR_SSL_CA, 1014 for MYSQL_ATTR_SSL_VERIFY_SERVER_CERT)
    // to prevent deprecation warnings in PHP 8.5+.
    $ca_paths = [
        '/etc/ssl/certs/ca-certificates.crt',
        '/etc/pki/tls/certs/ca-bundle.crt',
        '/etc/ssl/ca-bundle.pem',
        '/etc/ssl/cert.pem',
    ];
    $ca_file = '/dev/null';
    foreach ($ca_paths as $path) {
        if (file_exists($path)) {
            $ca_file = $path;
            break;
        }
    }
    $options[1007] = $ca_file;
    $options[1014] = false; // Disable verification to bypass missing certificate store issues
}

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
