<?php
// init_db.php - create the leads_semhys table if it doesn't exist
// Usage: init_db.php?token=THE_TOKEN

<?php
// init_db.php - DISABLED
// This endpoint has been intentionally disabled for security.
// See diag_DISABLED_README.txt for restoration instructions.

http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['ok' => false, 'error' => 'Disabled']);
exit;

$token_path = realpath(__DIR__ . '/../.diag_token');
if (!($token_path && is_readable($token_path))) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Token file not found']);
    exit;
}

$expected = trim(file_get_contents($token_path));
$given = $_GET['token'] ?? '';
if ($given !== $expected) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Invalid token']);
    exit;
}

header('Content-Type: application/json');
try {
    $mysqli = db();
    $sql = file_get_contents(__DIR__ . '/sql/leads_semhys.sql');
    if (!$sql) throw new Exception('SQL file missing');
    $mysqli->multi_query($sql);
    // consume results
    do { if ($res = $mysqli->store_result()) { $res->free(); } } while ($mysqli->more_results() && $mysqli->next_result());
    echo json_encode(['ok' => true, 'message' => 'Table created or already exists']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
