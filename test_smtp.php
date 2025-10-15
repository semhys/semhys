<?php
// test_smtp.php - prueba de envío SMTP (usa includes/config.php y PHPMailer)
require_once __DIR__ . '/includes/config.php';
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
