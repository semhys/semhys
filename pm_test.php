<?php
header('Content-Type: application/json; charset=utf-8');
$pm_local = __DIR__ . '/phpmailer/src/PHPMailer.php';
$pm_parent = __DIR__ . '/../phpmailer/src/PHPMailer.php';
$out = ['ok' => false];
if (file_exists($pm_local)) {
    require_once $pm_local;
    $out['pmailer_path'] = $pm_local;
} elseif (file_exists($pm_parent)) {
    require_once $pm_parent;
    $out['pmailer_path'] = $pm_parent;
} else {
    $out['error'] = 'phpmailer_missing';
    echo json_encode($out);
    exit;
}

$out['class_exists'] = class_exists('PHPMailer\\PHPMailer\\PHPMailer');
$out['ok'] = true;
echo json_encode($out, JSON_PRETTY_PRINT);
