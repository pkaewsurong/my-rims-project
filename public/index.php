<?php
// public/index.php

session_start();

// Simple Router
$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri);
$path = $parsed_url['path'] ?? '/';

// Dynamically determine the base path (for subfolder deployments like XAMPP)
$base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if ($base_path === '/') {
    $base_path = '';
}

$route = $path;
if ($base_path !== '' && strpos($route, $base_path) === 0) {
    $route = substr($route, strlen($base_path));
}
// Strip '/public' in case it's still prefixed from hardcoded HTML links
if (strpos($route, '/public') === 0) {
    $route = substr($route, 7);
}
if ($route === '') {
    $route = '/';
}

if ($route === '/test-db') {
    $skip_db_connect = true;
}

// Include core configurations
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Route handling
if ($route === '' || $route === '/') {
    require __DIR__ . '/../views/index.html';
} elseif ($route === '/test-db') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== DB Connection Diagnostic Test ===\n\n";

    echo "Host: " . (getenv('DB_HOST') ?: '127.0.0.1') . "\n";
    echo "Port: " . (getenv('DB_PORT') ?: '3306') . "\n";
    echo "DB Name: " . (getenv('DB_NAME') ?: 'project_is') . "\n";
    echo "DB User: " . (getenv('DB_USER') ?: 'root') . "\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "OpenSSL Loaded: " . (extension_loaded('openssl') ? 'Yes' : 'No') . "\n";
    echo "PDO Drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n";
    if (extension_loaded('openssl')) {
        echo "OpenSSL Version: " . OPENSSL_VERSION_TEXT . "\n";
        echo "OpenSSL Cert Locations:\n" . print_r(openssl_get_cert_locations(), true) . "\n";
    }

    // Capture phpinfo modules for mysqlnd and pdo_mysql
    ob_start();
    phpinfo(INFO_MODULES);
    $phpinfo = ob_get_clean();
    
    // Extract sections of interest using regex
    echo "=== phpinfo() pdo_mysql & mysqlnd sections ===\n";
    if (preg_match('/pdo_mysql(.*?)(\n\n|<h2)/s', strip_tags($phpinfo), $matches)) {
        echo "--- pdo_mysql ---\n" . trim($matches[1]) . "\n\n";
    } else {
        echo "--- pdo_mysql section not found ---\n\n";
    }
    if (preg_match('/mysqlnd(.*?)(\n\n|<h2)/s', strip_tags($phpinfo), $matches)) {
        echo "--- mysqlnd ---\n" . trim($matches[1]) . "\n\n";
    } else {
        echo "--- mysqlnd section not found ---\n\n";
    }
    echo "===============================================\n\n";

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'project_is';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

    $ca_file = dirname(__DIR__) . '/config/cacert.pem';
    echo "CA File Path: " . $ca_file . "\n";
    echo "CA File Exists: " . (file_exists($ca_file) ? 'Yes' : 'No') . "\n";
    if (file_exists($ca_file)) {
        echo "CA File Size: " . filesize($ca_file) . " bytes\n";
        echo "CA File Readable: " . (is_readable($ca_file) ? 'Yes' : 'No') . "\n";
    }
    echo "\n";

    // Test cases
    $tests = [
        "Case 1: No SSL Options" => [],
        "Case 2: SSL CA only (1007)" => [1007 => $ca_file],
        "Case 3: SSL Verify False only (1014 => false)" => [1014 => false],
        "Case 4: SSL CA & Verify False" => [1007 => $ca_file, 1014 => false],
        "Case 5: SSL CA & Verify True" => [1007 => $ca_file, 1014 => true],
        "Case 6: System CA (/etc/ssl/certs/ca-certificates.crt)" => [1007 => '/etc/ssl/certs/ca-certificates.crt'],
        "Case 7: System CA & Verify False" => [1007 => '/etc/ssl/certs/ca-certificates.crt', 1014 => false],
        "Case 10: SSL CA => true" => [1007 => true],
        "Case 11: SSL CA => true & Verify False" => [1007 => true, 1014 => false],
        "Case 12: SSL CA => true & Verify True" => [1007 => true, 1014 => true],
        "Case 13: Amazon Linux CA (/etc/pki/tls/cert.pem)" => [1007 => '/etc/pki/tls/cert.pem'],
        "Case 14: Amazon Linux CA & Verify False" => [1007 => '/etc/pki/tls/cert.pem', 1014 => false],
        "Case 15: Amazon Linux CA & Verify True" => [1007 => '/etc/pki/tls/cert.pem', 1014 => true],
        "Case 16: SSL CA => '' & Verify False" => [1007 => '', 1014 => false],
        "Case 17: SSL CA => '/dev/null' & Verify False" => [1007 => '/dev/null', 1014 => false],
    ];

    foreach ($tests as $name => $opts) {
        echo "--- Running $name ---\n";
        try {
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            foreach ($opts as $k => $v) {
                $options[$k] = $v;
            }
            $test_pdo = new PDO($dsn, $username, $password, $options);
            echo "SUCCESS: Connected successfully!\n";
            $stmt = $test_pdo->query("SELECT VERSION()");
            echo "Database Version: " . $stmt->fetchColumn() . "\n";
        } catch (\Exception $e) {
            echo "FAILED: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    // Try mysqli
    echo "--- Running Case 8: mysqli with SSL (CA) ---\n";
    try {
        $link = mysqli_init();
        mysqli_ssl_set($link, NULL, NULL, $ca_file, NULL, NULL);
        if (@mysqli_real_connect($link, $host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
            echo "SUCCESS: Connected successfully via mysqli!\n";
            $res = mysqli_query($link, "SELECT VERSION()");
            $row = mysqli_fetch_row($res);
            echo "Database Version: " . $row[0] . "\n";
            mysqli_close($link);
        } else {
            echo "FAILED: " . mysqli_connect_error() . "\n";
        }
    } catch (\Exception $e) {
        echo "FAILED (Exception): " . $e->getMessage() . "\n";
    }
    echo "\n";

    echo "--- Running Case 9: mysqli with SSL (No CA) ---\n";
    try {
        $link = mysqli_init();
        if (@mysqli_real_connect($link, $host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
            echo "SUCCESS: Connected successfully via mysqli (No CA)!\n";
            $res = mysqli_query($link, "SELECT VERSION()");
            $row = mysqli_fetch_row($res);
            echo "Database Version: " . $row[0] . "\n";
            mysqli_close($link);
        } else {
            echo "FAILED: " . mysqli_connect_error() . "\n";
        }
    } catch (\Exception $e) {
        echo "FAILED (Exception): " . $e->getMessage() . "\n";
    }
    echo "\n";

    exit();
} elseif ($route === '/login') {
    require __DIR__ . '/../src/controllers/AuthController.php';
    loginAction($pdo);
} elseif ($route === '/register') {
    require __DIR__ . '/../src/controllers/AuthController.php';
    registerAction($pdo);
} elseif ($route === '/logout') {
    require __DIR__ . '/../src/controllers/AuthController.php';
    logoutAction();
} elseif ($route === '/forgot-password') {
    require __DIR__ . '/../src/controllers/AuthController.php';
    forgotPasswordAction($pdo);
} elseif ($route === '/reset-password') {
    require __DIR__ . '/../src/controllers/AuthController.php';
    resetPasswordAction($pdo);
} elseif ($route === '/projects') {
    require __DIR__ . '/../src/controllers/ProjectController.php';
    indexAction($pdo);
} elseif ($route === '/projects/all') {
    require __DIR__ . '/../src/controllers/ProjectController.php';
    allAction($pdo);
} elseif (preg_match('#^/projects/(\d+)$#', $route, $matches)) {
    // Dynamic route for project details: /projects/{id}
    require __DIR__ . '/../src/controllers/ProjectController.php';
    showAction($pdo, $matches[1]);
} elseif ($route === '/projects/request-closure') {
    require __DIR__ . '/../src/controllers/ProjectController.php';
    requestClosureAction($pdo);
} elseif ($route === '/projects/approve-closure') {
    require __DIR__ . '/../src/controllers/ProjectController.php';
    approveClosureAction($pdo);
} elseif ($route === '/notifications/mark-read') {
    // Quick inline action for notifications
    if (isLoggedIn()) {
        $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
        $stmt->execute([authUser()['id']]);
    }
    $referer = $_SERVER['HTTP_REFERER'] ?? null;
    if ($referer) {
        header('Location: ' . $referer);
        exit();
    }
    redirect('/projects');
} elseif ($route === '/proposals') {
    require __DIR__ . '/../src/controllers/ProposalController.php';
    proposalIndexAction($pdo);
} elseif (preg_match('#^/proposals/(\d+)$#', $route, $matches)) {
    require __DIR__ . '/../src/controllers/ProposalController.php';
    proposalShowAction($pdo, $matches[1]);
} elseif (preg_match('#^/proposals/(\d+)/edit$#', $route, $matches)) {
    require __DIR__ . '/../src/controllers/ProposalController.php';
    proposalEditAction($pdo, $matches[1]);
} elseif (preg_match('#^/proposals/(\d+)/delete$#', $route, $matches)) {
    require __DIR__ . '/../src/controllers/ProposalController.php';
    proposalDeleteAction($pdo, $matches[1]);
} elseif ($route === '/proposals/create') {
    require __DIR__ . '/../src/controllers/ProposalController.php';
    proposalCreateAction($pdo);
} elseif ($route === '/proposals/store') {
    require __DIR__ . '/../src/controllers/ProposalController.php';
    proposalStoreAction($pdo);
} elseif ($route === '/proposals/update') {
    require __DIR__ . '/../src/controllers/ProposalController.php';
    proposalUpdateAction($pdo);
} elseif ($route === '/proposals/review') {
    require __DIR__ . '/../src/controllers/ProposalController.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        proposalStoreReviewAction($pdo);
    } else {
        proposalReviewAction($pdo);
    }
} elseif ($route === '/proposals/budget') {
    require __DIR__ . '/../src/controllers/BudgetController.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action_type']) && $_POST['action_type'] === 'source') {
            addSourceAction($pdo);
        } else {
            addLineItemAction($pdo);
        }
    } else {
        manageAction($pdo);
    }
} elseif ($route === '/project-files/store') {
    require __DIR__ . '/../src/controllers/FileController.php';
    fileStoreAction($pdo);
} elseif (preg_match('#^/project-files/(\d+)/destroy$#', $route, $matches)) {
    require __DIR__ . '/../src/controllers/FileController.php';
    fileDestroyAction($pdo, $matches[1]);
} elseif ($route === '/progress-reports/create') {
    require __DIR__ . '/../src/controllers/ProgressReportController.php';
    progressCreateAction($pdo);
} elseif ($route === '/progress-reports/store') {
    require __DIR__ . '/../src/controllers/ProgressReportController.php';
    progressStoreAction($pdo);
} elseif ($route === '/research-outputs/create') {
    require __DIR__ . '/../src/controllers/PublicationController.php';
    createAction($pdo);
} elseif ($route === '/research-outputs/store') {
    require __DIR__ . '/../src/controllers/PublicationController.php';
    storeAction($pdo);
} elseif ($route === '/ip-assets/create') {
    require __DIR__ . '/../src/controllers/IpController.php';
    createAction($pdo);
} elseif ($route === '/ip-assets/store') {
    require __DIR__ . '/../src/controllers/IpController.php';
    storeAction($pdo);
} elseif ($route === '/qa') {
    require __DIR__ . '/../src/controllers/QaController.php';
    indexAction($pdo);
} elseif ($route === '/qa/export') {
    require __DIR__ . '/../src/controllers/QaController.php';
    exportAction($pdo);
} elseif ($route === '/reports/final/create') {
    require __DIR__ . '/../src/controllers/FinalReportController.php';
    createAction($pdo);
} elseif ($route === '/reports/final/store') {
    require __DIR__ . '/../src/controllers/FinalReportController.php';
    storeAction($pdo);
} elseif ($route === '/archives') {
    require __DIR__ . '/../src/controllers/ArchiveController.php';
    indexAction($pdo);
} elseif ($route === '/archives/store') {
    require __DIR__ . '/../src/controllers/ArchiveController.php';
    storeAction($pdo);
} elseif ($route === '/archives/settings') {
    require __DIR__ . '/../src/controllers/ArchiveController.php';
    settingsStoreAction($pdo);
} elseif ($route === '/dashboard') {
    require __DIR__ . '/../src/controllers/DashboardController.php';
    researchDashboardAction($pdo);
} elseif ($route === '/metrics') {
    require __DIR__ . '/../src/controllers/MetricController.php';
    indexAction($pdo);
} elseif ($route === '/metrics/sync') {
    require __DIR__ . '/../src/controllers/MetricController.php';
    syncAction($pdo);
} elseif ($route === '/strategic-reports') {
    require __DIR__ . '/../src/controllers/StrategicReportController.php';
    strategicReportsAction($pdo);
} elseif ($route === '/strategic-reports/generate') {
    require __DIR__ . '/../src/controllers/StrategicReportController.php';
    generateReportAction($pdo);
} elseif ($route === '/profile') {
    require __DIR__ . '/../src/controllers/ProfileController.php';
    indexAction($pdo);
} elseif ($route === '/profile/edit') {
    require __DIR__ . '/../src/controllers/ProfileController.php';
    editAction($pdo);
} elseif ($route === '/profile/update') {
    require __DIR__ . '/../src/controllers/ProfileController.php';
    updateAction($pdo);
} elseif ($route === '/dashboard/researchers') {
    require __DIR__ . '/../src/controllers/DashboardController.php';
    researchersListAction($pdo);
} elseif ($route === '/admin/master-data') {
    require __DIR__ . '/../src/controllers/MasterDataController.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        masterDataStoreAction($pdo);
    } else {
        masterDataAction($pdo);
    }
} else {
    http_response_code(404);
    echo "404 Not Found";
}
