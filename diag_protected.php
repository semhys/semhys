<?php
// diag_protected.php - DISABLED
// This endpoint has been intentionally disabled for security.
// To restore: see diag_DISABLED_README.txt which explains how to place the
// real script back and how to securely store the token outside the webroot.

http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['ok' => false, 'error' => 'Diagnostic endpoint disabled', 'info' => 'See diag_DISABLED_README.txt']);
exit;
