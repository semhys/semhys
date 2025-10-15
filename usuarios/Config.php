<?php
/**
 * Configuración general SEMHYS (PRODUCCIÓN)
 */

declare(strict_types=1);

// Prefer a config placed one level above the webroot. Fall back to includes/config.php
$external = realpath(__DIR__ . '/../config.php');
if ($external && is_readable($external)) {
    require_once $external;
} else {
    require_once __DIR__ . '/../includes/config.php';
}

// --- Token de API (cámbialo cuando quieras) ---
const API_TOKEN = 'semhys-2025-indexador';

// Carpeta privada con documentos a indexar
// Detecta /private_docs al lado de public_html; si no existe, ajusta la ruta manual.
$DOCS_ROOT = realpath(__DIR__ . '/../private_docs');
if ($DOCS_ROOT === false || !is_dir($DOCS_ROOT)) {
    // <- si tu estructura es distinta, pon aquí la ruta absoluta correcta
    $DOCS_ROOT = '/home/u726518692/domains/semhys.com/private_docs';
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// db() is provided by the central config (or includes/config.php.example in dev).

function now_mysql(): string {
    return date('Y-m-d H:i:s');
}

function get_qs(string $key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}