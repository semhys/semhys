<?php
/**
 * Configuración general SEMHYS (PRODUCCIÓN)
 */

declare(strict_types=1);

// --- MySQL (según tus datos) ---
const DB_HOST = 'localhost';
const DB_NAME = 'u726518692_semhys_data';
const DB_USER = 'u726518692_semhys_user';
const DB_PASS = 'Q@semhys2025';

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

function db(): mysqli {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

function now_mysql(): string {
    return date('Y-m-d H:i:s');
}

function get_qs(string $key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}