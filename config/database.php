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
    // Disable server certificate verification because serverless environments (like AWS Lambda on Vercel)
    // do not have default CA certificates configured. The connection remains encrypted.
    // We use integer values (1007 for MYSQL_ATTR_SSL_CA, 1014 for MYSQL_ATTR_SSL_VERIFY_SERVER_CERT)
    // to prevent deprecation warnings in PHP 8.5+.
    $options[1007] = null;
    $options[1014] = false;
}

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
