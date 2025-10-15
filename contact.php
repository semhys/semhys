<?php $page='contact'; ?>
<!doctype html><html lang="es"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Contacto | SEMHYS</title>
<link rel="stylesheet" href="/assets/css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head><body>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="section"><div class="container">
<h2>Contáctanos</h2><p class="sub">Te responderemos a la brevedad.</p>
<form id="f">
  <label for="nombre">Nombre</label>
  <input type="text" id="nombre" name="nombre" required>
  <label for="correo">Correo</label>
  <input type="email" id="correo" name="correo" required>
  <label for="ciudad">Ciudad/Estado</label>
  <input type="text" id="ciudad" name="ciudad">
  <label for="mensaje">Mensaje</label>
  <textarea id="mensaje" name="mensaje" rows="4" required></textarea>
  <button type="submit" class="btn">Enviar</button>
  <div id="formStatus" style="margin-top:1em;"></div>
</form>
</div></section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script>
const N8N_WEBHOOK_URL = 'https://TU_SUBDOMINIO.app.n8n.cloud/webhook/REEMPLAZA_ESTO'; // Cambia por tu URL real
document.getElementById('f').addEventListener('submit', async function(e) {
  e.preventDefault();
  const status = document.getElementById('formStatus');
  status.textContent = 'Enviando...';
  const data = {
    nombre: this.nombre.value,
    correo: this.correo.value,
    ciudad: this.ciudad.value,
    mensaje: this.mensaje.value
  };
  try {
    const res = await fetch(N8N_WEBHOOK_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    if (res.ok) {
      status.textContent = '¡Mensaje enviado! Pronto te contactaremos.';
      this.reset();
    } else {
      status.textContent = 'Error al enviar. Intenta de nuevo.';
    }
  } catch {
    status.textContent = 'Error de conexión. Intenta más tarde.';
  }
});
</script>
</body></html>