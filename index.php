<?php $page='home'; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SEMHYS • Soluciones de Ingeniería</title>
  <meta property="og:image" content="/assets/img/logo.svg" />
  <meta name="twitter:image" content="/assets/img/logo.svg" />
  <meta itemprop="image" content="/assets/img/logo.svg" />
  <meta name="author" content="SEMHYS" />
  <meta name="organization" content="SEMHYS" />
  <link rel="stylesheet" href="/assets/css/styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <section id="home" class="hero">
    <div class="container hero-grid">
      <div class="hero_copy">
          <h1 id="hero_title">Soluciones de Ingeniería Sostenible</h1>
          <p id="hero_desc">Diseño, automatización y ejecución de proyectos hidráulicos, eléctricos y mecánicos con estándares internacionales y enfoque en eficiencia energética.</p>
        <div class="hero_cta">
            <a id="btn_services" href="#services" class="btn btn-primary">Ver servicios</a>
            <a id="btn_quote" href="/contact.html" class="btn btn-ghost">Solicitar cotización</a>
        </div>
        <div class="hero_badges">
          <span>ISO 9001</span>
          <span>Estándares IEC</span>
          <span>QA/QC</span>
        </div>
      </div>

      <div class="mockup-card" aria-hidden="true">
        <div class="mockup-card__body">
          <div class="stats">
            <div><strong>18%</strong> ahorro energético</div>
            <div><strong>12</strong> redes SCADA</div>
            <div><strong>+30</strong> EPC entregados</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="services" class="section">
    <div class="container section__head">
      <h2 id="services_title">Nuestros servicios</h2>
      <p id="services_desc">Ingeniería • Hidráulica • Eléctrica • Automatización • Consultoría • EPC • Mantenimiento</p>
    </div>

    <div class="container grid-3">
      <article class="card">
        <h3>Hidráulica</h3>
        <p>Modelación, bombeo, tratamiento de agua y redes.</p>
      </article>
      <article class="card">
        <h3>Eléctrica</h3>
        <p>BT/MT, tableros, protecciones, IEC, selectividad y eficiencia.</p>
      </article>
      <article class="card">
        <h3>Automatización</h3>
        <p>SCADA, PLC, IIoT, telemetría, control y analítica.</p>
      </article>
    </div>
  </section>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
