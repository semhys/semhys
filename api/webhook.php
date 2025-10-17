<?php
// api/webhook.php
// Public-facing proxy that forwards POSTs to n8n and injects a server-side secret.
// This is a duplicate of includes/webhook_proxy.php but located in a public API path so it
// can be reached by external clients.

// Load secret from file outside webroot if present, then from environment, then fallback
$n8nUrl = 'https://semhys.app.n8n.cloud/webhook/semhys-contact';
$secret = null;
$secretFile = __DIR__ . '/../webhook_secret.php';
if (file_exists($secretFile)) {
    // file should define $WEBHOOK_SECRET (keep this file outside public_html)
    include_once $secretFile;
    if (!empty($WEBHOOK_SECRET)) {
        $secret = $WEBHOOK_SECRET;
    }
}
if ($secret === null) {
    // try environment variable
    $secret = getenv('WEBHOOK_SECRET') ?: 'MI_TOKEN_LOCAL_DE_PRUEBA';
}

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
if (preg_match('/Content-Type:\s*([^\r\n]+)/i', $respHeaders, $m)) {
    header('Content-Type: ' . trim($m[1]));
} else {
    header('Content-Type: application/json');
}

echo $respBody;

// --- Simple logging ---
function proxy_log(array $entry) {
    // place log alongside includes directory (../includes/webhook_proxy.log)
    $logFile = __DIR__ . '/../includes/webhook_proxy.log';
    $line = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
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
