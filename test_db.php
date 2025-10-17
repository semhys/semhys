<?php
<?php
// test_db.php - DISABLED
// This endpoint has been intentionally disabled for security.
// See diag_DISABLED_README.txt for restoration instructions.

http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['ok' => false, 'error' => 'Disabled']);
exit;

header('Content-Type: application/json');
$out = ['ok' => false, 'error' => null];
try {
    $mysqli = db();
    $res = $mysqli->query('SELECT NOW() as now');
    if ($res) {
        $row = $res->fetch_assoc();
        $out['ok'] = true;
        $out['now'] = $row['now'];
    } else {
        $out['error'] = $mysqli->error;
    }
    <?php
    // Robust DB tester that returns JSON even on fatal errors.
    header('Content-Type: application/json; charset=utf-8');
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    $response = ['ok' => false];

    // Collect errors and output as JSON on shutdown
    register_shutdown_function(function() use (&$response) {
        $err = error_get_last();
        if ($err) {
            $response['fatal_error'] = $err;
            http_response_code(500);
            echo json_encode($response);
            return;
        }
        // If nothing fatal and not already output, emit response
        if (!headers_sent()) {
            echo json_encode($response);
        }
    });

    set_error_handler(function($errno, $errstr, $errfile, $errline) use (&$response) {
        // record non-fatal errors
        $response['error'] = ['type' => $errno, 'message' => $errstr, 'file' => $errfile, 'line' => $errline];
        // do not exit; let shutdown handler emit JSON
        return true;
    });

    try {
        // Prefer config one level above public_html, fallback to includes/config.php
        $cfg_paths = [__DIR__ . '/../config.php', __DIR__ . '/includes/config.php'];
        $loaded = false;
        foreach ($cfg_paths as $p) {
            if (file_exists($p)) {
                include $p;
                $loaded = true;
                $response['config_path'] = $p;
                break;
            }
        }

        if (!$loaded) {
            $response['error'] = 'config_not_found';
            $response['paths_checked'] = $cfg_paths;
            exit;
        }

        // Resolve DB constants/vars
        $dbHost = defined('DB_HOST') ? DB_HOST : (isset($DB_HOST) ? $DB_HOST : getenv('DB_HOST'));
        $dbUser = defined('DB_USER') ? DB_USER : (isset($DB_USER) ? $DB_USER : getenv('DB_USER'));
        $dbPass = defined('DB_PASS') ? DB_PASS : (isset($DB_PASS) ? $DB_PASS : getenv('DB_PASS'));
        $dbName = defined('DB_NAME') ? DB_NAME : (isset($DB_NAME) ? $DB_NAME : getenv('DB_NAME'));

        $response['checked'] = ['db_host' => $dbHost, 'db_user' => $dbUser, 'db_name' => $dbName];

        $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        if ($mysqli->connect_errno) {
            $response['connect_error_no'] = $mysqli->connect_errno;
            $response['connect_error'] = $mysqli->connect_error;
            exit;
        }

        $response['ok'] = true;
        $response['server_info'] = $mysqli->server_info ?? null;

        $q = $mysqli->query("SELECT 1 AS test");
        if ($q) {
            $response['query_test'] = $q->fetch_assoc();
        } else {
            $response['query_test_error'] = $mysqli->error;
        }

        $mysqli->close();

    } catch (Throwable $e) {
        $response['exception'] = ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()];
    }
