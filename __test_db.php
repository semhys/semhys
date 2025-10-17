<?php
header('Content-Type: application/json; charset=utf-8');
// Simple DB connection tester. Prefers ../config.php (outside webroot) if present.
$cfg_paths = [__DIR__ . '/../config.php', __DIR__ . '/includes/config.php'];
$loaded = false;
foreach ($cfg_paths as $p) {
    if (file_exists($p)) {
        include $p;
        $loaded = true;
        break;
    }
}

if (!$loaded) {
    echo json_encode(['ok' => false, 'error' => 'config_not_found', 'paths_checked' => $cfg_paths]);
    exit(0);
}

// Expecting DB_HOST, DB_USER, DB_PASS, DB_NAME constants or variables.
$dbHost = defined('DB_HOST') ? DB_HOST : (isset($DB_HOST) ? $DB_HOST : '');
$dbUser = defined('DB_USER') ? DB_USER : (isset($DB_USER) ? $DB_USER : '');
$dbPass = defined('DB_PASS') ? DB_PASS : (isset($DB_PASS) ? $DB_PASS : '');
$dbName = defined('DB_NAME') ? DB_NAME : (isset($DB_NAME) ? $DB_NAME : '');

$result = ['ok' => false, 'checked' => ['db_host' => $dbHost, 'db_user' => $dbUser, 'db_name' => $dbName]];

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_errno) {
    $result['error'] = 'connect_failed';
    $result['connect_error_no'] = $mysqli->connect_errno;
    $result['connect_error'] = $mysqli->connect_error;
    echo json_encode($result);
    exit(0);
}

$result['ok'] = true;
$result['server_info'] = $mysqli->server_info;
$result['server_version'] = $mysqli->server_version;

// quick query test
$q = $mysqli->query("SELECT 1 AS test");
if ($q) {
    $row = $q->fetch_assoc();
    $result['query_test'] = $row;
} else {
    $result['query_test_error'] = $mysqli->error;
}

$mysqli->close();
echo json_encode($result);
