<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

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
$rows = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$totalPages = ($total===0?1:(int)ceil($total/$limit));
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Documentos — SEMHYS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:20px;color:#222}
  h1{margin:0 0 16px}
  table{width:100%;border-collapse:collapse;margin-top:12px}
  th,td{padding:8px 10px;border-bottom:1px solid #eee;font-size:14px;vertical-align:top}
  th{background:#fafafa;text-align:left}
  .toolbar{display:flex;gap:10px;align-items:center;margin-bottom:10px;flex-wrap:wrap}
  input[type="text"],input[type="number"]{padding:6px 8px;border:1px solid #ccc;border-radius:6px}
  button{padding:8px 12px;border:0;border-radius:8px;background:#0b5fff;color:#fff;cursor:pointer}
  .muted{color:#777}
  .pagination{display:flex;gap:10px;align-items:center;margin-top:12px}
  .pill{display:inline-block;padding:.25rem .5rem;border-radius:999px;background:#eef;border:1px solid #dde;color:#334;text-decoration:none}
  .mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace}
  .nowrap{white-space:nowrap}
</style>
</head>
<body>
  <h1>Documentos (<?= (int)$total ?>)</h1>

  <form class="toolbar" method="get">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar en path…" size="32">
    <input type="number" name="limit" min="1" max="100" value="<?= (int)$limit ?>">
    <button>Buscar</button>
    <span class="muted">Página <?= (int)$page ?> de <?= (int)$totalPages ?></span>
    <a class="pill" href="api_docs.php?token=<?= urlencode(API_TOKEN) ?>&q=<?= urlencode($q) ?>&limit=<?= (int)$limit ?>&page=<?= (int)$page ?>">Ver JSON</a>
  </form>

  <table>
    <thead>
      <tr>
        <th class="nowrap">id</th>
        <th>path</th>
        <th class="nowrap">size_bytes</th>
        <th class="nowrap">created_at</th>
        <th class="nowrap">updated_at</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td class="mono nowrap"><?= (int)$r['id'] ?></td>
        <td class="mono"><?= htmlspecialchars($r['path']) ?></td>
        <td class="mono nowrap"><?= number_format((int)$r['size_bytes'], 0, ',', '.') ?></td>
        <td class="mono nowrap"><?= htmlspecialchars($r['created_at']) ?></td>
        <td class="mono nowrap"><?= htmlspecialchars($r['updated_at']) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?>
      <tr><td colspan="5" class="muted">Sin resultados.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="pagination">
    <?php if ($page>1): ?>
      <a class="pill" href="?q=<?= urlencode($q) ?>&limit=<?= (int)$limit ?>&page=<?= $page-1 ?>">« Anterior</a>
    <?php endif; ?>
    <?php if ($page<$totalPages): ?>
      <a class="pill" href="?q=<?= urlencode($q) ?>&limit=<?= (int)$limit ?>&page=<?= $page+1 ?>">Siguiente »</a>
    <?php endif; ?>
  </div>
</body>
</html>