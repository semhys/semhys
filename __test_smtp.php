<?php
header('Content-Type: application/json; charset=utf-8');
// Simple SMTP tester using PHPMailer from the bundled phpmailer/ folder.
$cfg_paths = [__DIR__ . '/../config.php', __DIR__ . '/includes/config.php'];
$loaded = false;
foreach ($cfg_paths as $p) {
    if (file_exists($p)) {
        include $p;
        $loaded = true;
        break;
    }
}

if (!$loaded) {
    echo json_encode(['ok' => false, 'error' => 'config_not_found', 'paths_checked' => $cfg_paths]);
    exit(0);
}

// Adjust these to match your config: expect SMTP_HOST, SMTP_USER, SMTP_PASS, SMTP_PORT, SMTP_SECURE, FROM_EMAIL
$smtpHost = defined('SMTP_HOST') ? SMTP_HOST : (isset($SMTP_HOST) ? $SMTP_HOST : '');
$smtpUser = defined('SMTP_USER') ? SMTP_USER : (isset($SMTP_USER) ? $SMTP_USER : '');
$smtpPass = defined('SMTP_PASS') ? SMTP_PASS : (isset($SMTP_PASS) ? $SMTP_PASS : '');
$smtpPort = defined('SMTP_PORT') ? SMTP_PORT : (isset($SMTP_PORT) ? $SMTP_PORT : 587);
$smtpSecure = defined('SMTP_SECURE') ? SMTP_SECURE : (isset($SMTP_SECURE) ? $SMTP_SECURE : 'tls');
$from = defined('FROM_EMAIL') ? FROM_EMAIL : (isset($FROM_EMAIL) ? $FROM_EMAIL : $smtpUser);

// Basic checks
$result = ['ok' => false, 'checked' => ['host' => $smtpHost, 'user' => $smtpUser, 'port' => $smtpPort, 'secure' => $smtpSecure, 'from' => $from]];

// Load PHPMailer
if (!is_dir(__DIR__ . '/phpmailer') && !is_dir(__DIR__ . '/../phpmailer')) {
    $result['error'] = 'phpmailer_not_found';
    echo json_encode($result);
    exit(0);
}

$pm_path = is_dir(__DIR__ . '/phpmailer') ? __DIR__ . '/phpmailer/src/' : __DIR__ . '/../phpmailer/src/';
require $pm_path . 'PHPMailer.php';
require $pm_path . 'SMTP.php';
require $pm_path . 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    $mail->SMTPSecure = $smtpSecure;
    $mail->Port = (int)$smtpPort;
    $mail->setFrom($from);
    $mail->addAddress($from);
    $mail->Subject = 'SMTP test';
    $mail->Body = 'This is a one-time SMTP test from __test_smtp.php';
    $mail->send();
    $result['ok'] = true;
    $result['message'] = 'Message sent (or accepted by SMTP server)';
} catch (Exception $e) {
    $result['error'] = 'phpmailer_exception';
    $result['exception_message'] = $e->getMessage();
    $result['smtp_debug'] = $mail->getSMTPInstance() ? $mail->getSMTPInstance()->getLastTransaction() : null;
}

echo json_encode($result);
