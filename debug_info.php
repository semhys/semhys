<?php
// Safe diagnostic endpoint (no secrets). Upload to public_html and open to get JSON about environment.
header('Content-Type: application/json; charset=utf-8');
$info = [];
$info['php_version'] = phpversion();
$info['php_sapi'] = php_sapi_name();
$info['loaded_extensions'] = get_loaded_extensions();

// check config readability (one level above public_html)
$cfg_path = realpath(__DIR__ . '/../config.php');
$info['config_realpath'] = $cfg_path ?: null;
$info['config_readable'] = is_readable(__DIR__ . '/../config.php');

// check phpmailer presence
$pm_local = __DIR__ . '/phpmailer/src/PHPMailer.php';
$pm_parent = __DIR__ . '/../phpmailer/src/PHPMailer.php';
$info['phpmailer_local_exists'] = file_exists($pm_local);
$info['phpmailer_parent_exists'] = file_exists($pm_parent);

// test include config (safe: don't print its contents)
$info['config_include_test'] = null;
try {
    if (file_exists(__DIR__ . '/../config.php')) {
        // attempt to include in a limited scope
        $res = @include_once __DIR__ . '/../config.php';
        $info['config_include_test'] = $res === 1 || $res === true ? 'included' : 'included_return_' . var_export($res, true);
    } else {
        $info['config_include_test'] = 'not_found';
    }
} catch (Throwable $e) {
    $info['config_include_exception'] = $e->getMessage();
}

// check mysqli extension
$info['mysqli_loaded'] = extension_loaded('mysqli');
$info['openssl_loaded'] = extension_loaded('openssl');

echo json_encode($info, JSON_PRETTY_PRINT);
