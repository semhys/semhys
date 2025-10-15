<?php
header('Content-Type: application/json; charset=UTF-8');

// Ajusta las credenciales si difieren en tu hosting
$dbHost = 'localhost';
$dbUser = 'u726518692_semhys_user';
$dbPass = 'Q@semhys2025';
$dbName = 'u726518692_semhys_data';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
	echo json_encode(['ok' => false, 'error' => 'db_connect']);
	exit;
}

$conn->set_charset('utf8mb4');

try {
	$res = $conn->query("SELECT * FROM leads_semhys ORDER BY id DESC LIMIT 100");
	$data = [];
	while ($r = $res->fetch_assoc()) {
		$data[] = $r;
	}
	echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
	echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

$conn->close();
