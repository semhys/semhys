<?php
<?php
// test_smtp.php - DISABLED
// This endpoint has been intentionally disabled for security.
// See diag_DISABLED_README.txt for restoration instructions.

http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['ok' => false, 'error' => 'Disabled']);
exit;
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
$out = ['ok' => false, 'error' => null];

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = 'tls';
    $mail->Port = SMTP_PORT;

    $mail->setFrom(FROM_EMAIL, FROM_NAME);
    $mail->addAddress(FROM_EMAIL);
    $mail->Subject = 'Prueba SMTP desde test_smtp.php';
    $mail->Body = 'Si recibes este correo, SMTP funciona correctamente.';

    $mail->send();
    $out['ok'] = true;
} catch (Exception $e) {
    $out['error'] = $e->getMessage();
}

echo json_encode($out);
