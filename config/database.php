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
    // Use the bundled DigiCert Global Root G2 certificate downloaded specifically for TiDB Cloud.
    // We use integer values (1007 for MYSQL_ATTR_SSL_CA, 1014 for MYSQL_ATTR_SSL_VERIFY_SERVER_CERT)
    // to prevent deprecation warnings in PHP 8.5+.
    $ca_file = __DIR__ . '/cacert.pem';
    if (!file_exists($ca_file)) {
        die("Database connection failed: CA Certificate file not found at " . $ca_file);
    }
    $options[1007] = $ca_file;
    $options[1014] = false; // Disable verification for serverless environment compatibility
}

if (!isset($skip_db_connect) || !$skip_db_connect) {
    try {
        $pdo = new PDO($dsn, $username, $password, $options);
    } catch (\PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
