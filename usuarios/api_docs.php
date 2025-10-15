<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

/**
 * API JSON privada.
 * Autenticación: ?token=API_TOKEN
 * Parámetros: q, limit(1..100), page(1..N)
 */

header('Content-Type: application/json; charset=utf-8');

if (get_qs('token') !== API_TOKEN) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'error'=>'Unauthorized']);
    exit;
}

$q = trim((string) get_qs('q',''));
$limit = max(1, min(100, (int) get_qs('limit', 20)));
$page  = max(1, (int) get_qs('page', 1));
$offset = ($page-1)*$limit;

$where = '1';
$params = [];
$types  = '';

if ($q !== '') {
    $where .= ' AND path LIKE ?';
    $params[] = '%'.$q.'%';
    $types   .= 's';
}

$sqlCount = "SELECT COUNT(*) FROM documents WHERE $where";
$stmt = db()->prepare($sqlCount);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

$sql = "
  SELECT id, path, size_bytes, created_at, updated_at
  FROM documents
  WHERE $where
  ORDER BY updated_at DESC, id DESC
  LIMIT ? OFFSET ?
";
$params2 = $params; $types2 = $types.'ii';
$params2[] = $limit; $params2[] = $offset;

$stmt = db()->prepare($sql);
$stmt->bind_param($types2, ...$params2);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) $data[] = $row;
$stmt->close();

echo json_encode([
  'ok'=>true,
  'data'=>$data,
  'pagination'=>[
    'q'=>$q,'limit'=>$limit,'page'=>$page,
    'total'=>(int)$total,
    'total_pages'=>($total===0?1:(int)ceil($total/$limit)),
    'has_prev'=>$page>1,
    'has_next'=>$offset+$limit<$total
  ],
  'meta'=>['source'=>'semhys_docs','generated_at'=>now_mysql()]
], JSON_UNESCAPED_UNICODE);