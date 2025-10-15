<?php $page='academy'; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Academia | SEMHYS</title>
  <link rel="stylesheet" href="/assets/css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <section class="section"><div class="container">
  <h2>Academia SEMHYS</h2><p class="sub">Capacitación en hidráulica, SCADA y eficiencia energética.</p>
  <div class="grid-2">
    <div class="card"><h3>Hidráulica Aplicada</h3><p>Diseño y operación de redes.</p><button class="btn" onclick="startAdvisory('Hidráulica Aplicada')">Asesor IA</button></div>
    <div class="card"><h3>SCADA & Telemetría</h3><p>Arquitecturas y ciberseguridad.</p><button class="btn" onclick="startAdvisory('SCADA & Telemetría')">Asesor IA</button></div>
  </div>
  <p class="note">Primero conversas con el <strong>Asistente SEMHYS</strong> para perfilar tu nivel y objetivo. Luego te inscribes.</p>
  </div></section>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script>
  function startAdvisory(course){
    fetch('https://semhys.app.n8n.cloud/webhook/chat_semhys', {
      method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ type:'academy_interest', course, page:location.pathname })
    }).catch(()=>{});
    const fab = document.querySelector('.chat-fab'); if(fab) fab.click();
  }
  </script>
</body></html>
