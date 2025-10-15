<?php $page='blog'; ?>
<!doctype html><html lang="es"><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Blog | SEMHYS</title>
<link rel="stylesheet" href="/assets/css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head><body>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="section"><div class="container">
<h2>Blog</h2><p class="sub">Actualidad técnica y casos de estudio.</p>
<div class="grid-3" id="posts">
  <div class="card"><h3>Optimización de bombeo</h3><p>Cómo reducir consumo y cavitación.</p></div>
  <div class="card"><h3>SCADA municipal</h3><p>Arquitecturas resilientes en agua potable.</p></div>
  <div class="card"><h3>PTAR compactas</h3><p>Claves para cumplir normativas actuales.</p></div>
</div>
</div></section>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body></html>