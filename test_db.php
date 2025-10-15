<?php
// test_db.php - prueba de conexión a la DB usando includes/config.php
require_once __DIR__ . '/includes/config.php';

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
