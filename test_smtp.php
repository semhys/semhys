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

<?php
// Robust SMTP tester that returns JSON even on fatal errors.
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$response = ['ok' => false];

register_shutdown_function(function() use (&$response) {
    $err = error_get_last();
    if ($err) {
        $response['fatal_error'] = $err;
        http_response_code(500);
        echo json_encode($response);
        return;
    }
    if (!headers_sent()) echo json_encode($response);
});

set_error_handler(function($errno, $errstr, $errfile, $errline) use (&$response) {
    $response['error'] = ['type' => $errno, 'message' => $errstr, 'file' => $errfile, 'line' => $errline];
    return true;
});

try {
    $cfg_paths = [__DIR__ . '/../config.php', __DIR__ . '/includes/config.php'];
    $loaded = false;
    foreach ($cfg_paths as $p) {
        if (file_exists($p)) {
            include $p;
            $loaded = true;
            $response['config_path'] = $p;
            break;
        }
    }
    if (!$loaded) {
        $response['error'] = 'config_not_found';
        $response['paths_checked'] = $cfg_paths;
        exit;
    }

    $smtpHost = defined('SMTP_HOST') ? SMTP_HOST : (isset($SMTP_HOST) ? $SMTP_HOST : getenv('SMTP_HOST'));
    $smtpUser = defined('SMTP_USER') ? SMTP_USER : (isset($SMTP_USER) ? $SMTP_USER : getenv('SMTP_USER'));
    $smtpPass = defined('SMTP_PASS') ? SMTP_PASS : (isset($SMTP_PASS) ? $SMTP_PASS : getenv('SMTP_PASS'));
    $smtpPort = defined('SMTP_PORT') ? SMTP_PORT : (isset($SMTP_PORT) ? $SMTP_PORT : getenv('SMTP_PORT'));
    $smtpSecure = defined('SMTP_SECURE') ? SMTP_SECURE : (isset($SMTP_SECURE) ? $SMTP_SECURE : getenv('SMTP_SECURE'));
    $from = defined('FROM_EMAIL') ? FROM_EMAIL : (isset($FROM_EMAIL) ? $FROM_EMAIL : $smtpUser);

    $response['checked'] = ['host' => $smtpHost, 'user' => $smtpUser, 'port' => $smtpPort, 'secure' => $smtpSecure, 'from' => $from];

    // Locate PHPMailer
    $pm_local = __DIR__ . '/phpmailer/src/PHPMailer.php';
    $pm_parent = __DIR__ . '/../phpmailer/src/PHPMailer.php';
    if (!file_exists($pm_local) && !file_exists($pm_parent)) {
        $response['error'] = 'phpmailer_not_found';
        exit;
    }
    $pm_path = file_exists($pm_local) ? __DIR__ . '/phpmailer/src/' : __DIR__ . '/../phpmailer/src/';
    require_once $pm_path . 'PHPMailer.php';
    require_once $pm_path . 'SMTP.php';
    require_once $pm_path . 'Exception.php';

    // Use PHPMailer
    \PHPMailer\PHPMailer\PHPMailer::DEBUG_SERVER; // ensure class autoloaded
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    if (!empty($smtpSecure)) $mail->SMTPSecure = $smtpSecure;
    if (!empty($smtpPort)) $mail->Port = (int)$smtpPort;
    $mail->setFrom($from);
    $mail->addAddress($from);
    $mail->Subject = 'SMTP test';
    $mail->Body = 'This is a one-time SMTP test from test_smtp.php';

    // Attempt send
    try {
        $mail->send();
        $response['ok'] = true;
        $response['message'] = 'Message sent (or accepted by SMTP server)';
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        $response['error'] = 'phpmailer_exception';
        $response['exception_message'] = $e->getMessage();
        $response['mailer_errorinfo'] = property_exists($mail, 'ErrorInfo') ? $mail->ErrorInfo : null;
    }

} catch (Throwable $e) {
    $response['exception'] = ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()];
}
