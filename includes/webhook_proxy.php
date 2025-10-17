<?php
// includes/webhook_proxy.php
// Simple proxy that forwards incoming JSON POSTs to the configured n8n webhook URL
// and injects a server-side secret header to protect the webhook.
//
// Usage:
// 1) Place this file under your site (e.g. includes/webhook_proxy.php).
// 2) Configure your form to POST to /includes/webhook_proxy.php instead of directly to n8n.
// 3) Set the environment variable WEBHOOK_SECRET on your server (recommended) or edit below.
//
// Notes: This proxy is intentionally small. For production, add rate-limiting, logging,
// and stricter validation (content-type checks, input size limits) as needed.

$secret = getenv('WEBHOOK_SECRET') ?: 'MI_TOKEN_LOCAL_DE_PRUEBA';
$n8nUrl = 'https://semhys.app.n8n.cloud/webhook/semhys-contact';

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Read raw body
$body = file_get_contents('php://input');
if ($body === false || strlen($body) === 0) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Empty body']);
    exit;
}

// Basic content-type check
$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') === false) {
    // Accept form-encoded too by converting
    if (stripos($contentType, 'application/x-www-form-urlencoded') !== false) {
        parse_str($body, $parsed);
        $body = json_encode($parsed);
    } else {
        // Not JSON nor form-encoded
        http_response_code(415);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unsupported Media Type']);
        exit;
    }
}

// Forward to n8n with cURL
$ch = curl_init($n8nUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true); // capture headers
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Webhook-Token: ' . $secret,
]);

$response = curl_exec($ch);
if ($response === false) {
    $err = curl_error($ch);
    curl_close($ch);
    http_response_code(502);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'proxy-error', 'detail' => $err]);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$respHeaders = substr($response, 0, $headerSize);
$respBody = substr($response, $headerSize);
curl_close($ch);

// Passthrough response
http_response_code($httpCode);
// Try to pass content-type from n8n response if present
if (preg_match('/Content-Type:\s*([^\r\n]+)/i', $respHeaders, $m)) {
    header('Content-Type: ' . trim($m[1]));
} else {
    header('Content-Type: application/json');
}

echo $respBody;

// --- Simple logging ---
// Append a compact JSON line to includes/webhook_proxy.log for auditing. This log
// intentionally records limited request/response detail to help triage invalid
// attempts. Do not log secrets in plain text in production.
function proxy_log(array $entry) {
    $logFile = __DIR__ . '/webhook_proxy.log';
    $line = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    // Best-effort write
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

$clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$clientProvidedToken = isset($_SERVER['HTTP_X_WEBHOOK_TOKEN']) || isset($_SERVER['HTTP_X-WEBHOOK-TOKEN']) || false;
$entry = [
    'ts' => gmdate('c'),
    'client_ip' => $clientIp,
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'POST',
    'path' => $_SERVER['REQUEST_URI'] ?? '',
    'content_type' => $contentType ?? '',
    'client_sent_token' => $clientProvidedToken,
    'forwarded_to' => $n8nUrl,
    'n8n_http_code' => $httpCode ?? null,
    'n8n_response_preview' => isset($respBody) ? mb_substr($respBody, 0, 100) : null,
];

proxy_log($entry);
