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
    $mysqli->close();
} catch (Exception $e) {
    $out['error'] = $e->getMessage();
}

echo json_encode($out);
