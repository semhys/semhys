<?php
header('Content-Type: application/json; charset=utf-8');
// Minimal DB connection tester. Safe: does not echo config contents.
@include __DIR__ . '/../config.php';

$dbHost = defined('DB_HOST') ? DB_HOST : (isset($DB_HOST) ? $DB_HOST : getenv('DB_HOST'));
$dbUser = defined('DB_USER') ? DB_USER : (isset($DB_USER) ? $DB_USER : getenv('DB_USER'));
$dbPass = defined('DB_PASS') ? DB_PASS : (isset($DB_PASS) ? $DB_PASS : getenv('DB_PASS'));
$dbName = defined('DB_NAME') ? DB_NAME : (isset($DB_NAME) ? $DB_NAME : getenv('DB_NAME'));

$out = ['ok' => false, 'checked' => ['db_host' => $dbHost, 'db_user' => $dbUser, 'db_name' => $dbName]];

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_errno) {
    $out['error'] = 'connect_failed';
    $out['connect_errno'] = $mysqli->connect_errno;
    $out['connect_error'] = $mysqli->connect_error;
    echo json_encode($out);
    exit;
}

$out['ok'] = true;
$out['server_info'] = $mysqli->server_info ?? null;

$q = $mysqli->query('SELECT 1 AS test');
if ($q) $out['query_test'] = $q->fetch_assoc();
else $out['query_test_error'] = $mysqli->error;

$mysqli->close();

echo json_encode($out, JSON_PRETTY_PRINT);
