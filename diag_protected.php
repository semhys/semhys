<?php
// diag_protected.php - herramienta de diagnóstico protegida por token
// Uso: diag_protected.php?token=TU_TOKEN&action=info|write|send

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';

define('DIAG_TOKEN', 'b9f7a1d2c4e6f8a0b3c5d7e9f1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6d7e8f9a');

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
