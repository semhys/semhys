<?php
// Minimal health check: does not include config.php. Safe to upload.
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'ok' => true,
    'time' => time(),
    'php_version' => phpversion(),
]);
