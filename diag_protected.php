<?php
// diag_protected.php - DISABLED
// This endpoint has been intentionally disabled for security.
// See diag_DISABLED_README.txt for restoration instructions.

http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['ok' => false, 'error' => 'Diagnostic endpoint disabled', 'info' => 'See diag_DISABLED_README.txt']);
exit;

// diag_protected.php - herramienta de diagnóstico protegida por token
// Uso: diag_protected.php?token=TU_TOKEN&action=info|write|send
<?php
// diag_protected.php - herramienta de diagnóstico protegida por token
// Uso: diag_protected.php?token=TU_TOKEN&action=info|write|send

// Prefer a config file placed outside the webroot (one level up). Fall back to includes/config.php for local dev.
$external_config = realpath(__DIR__ . '/../config.php');
if ($external_config && is_readable($external_config)) {
    require_once $external_config;
} else {
    require_once __DIR__ . '/includes/config.php';
}
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';

// Load DIAG_TOKEN from a file outside the webroot for safety.
$token_path = realpath(__DIR__ . '/../.diag_token');
if ($token_path && is_readable($token_path)) {
    $token_value = trim(file_get_contents($token_path));
    if ($token_value === '') {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Token file is empty.']);
        exit;
    }
    define('DIAG_TOKEN', $token_value);
} else {
    // If token file not found, do not expose diagnostics.
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Diagnostic token not configured. Place a .diag_token file outside public_html with the token.']);
    exit;
}

header('Content-Type: application/json');
$out = ['ok' => false, 'error' => null, 'action' => null];

$token = $_GET['token'] ?? '';
if (!$token || $token !== DIAG_TOKEN) {
    http_response_code(403);
    $out['error'] = 'Token inválido';
    echo json_encode($out);
    exit;
}

$action = $_GET['action'] ?? 'info';
$out['action'] = $action;

try {
    if ($action === 'info') {
        $out['php_version'] = phpversion();
        $out['mysqli'] = [];
        $mysqli = db();
        $out['mysqli']['connected'] = $mysqli->ping();
        $mysqli->close();
        $out['ok'] = true;
    } elseif ($action === 'write') {
        $mysqli = db();
        $sql = "INSERT INTO leads_semhys (name, email, message) VALUES ('Diag Test','diag@example.com','test')";
        $res = $mysqli->query($sql);
        if ($res) {
            $out['ok'] = true;
            $out['insert_id'] = $mysqli->insert_id;
        } else {
            $out['error'] = $mysqli->error;
        }
        $mysqli->close();
    } elseif ($action === 'send') {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port = SMTP_PORT;
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress(FROM_EMAIL);
        $mail->Subject = 'Diag send test';
        $mail->Body = 'Mensaje de prueba desde diag_protected.php';
        $mail->send();
        $out['ok'] = true;
    } else {
        $out['error'] = 'Acción no reconocida';
    }
} catch (Exception $e) {
    $out['error'] = $e->getMessage();
}

echo json_encode($out);
