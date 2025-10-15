<?php
// includes/header.php
// Header fragment used across pages. Set $page = 'home'|'about'|... before including.
$page = $page ?? '';
?>
<header class="site-header" role="banner">
  <div class="container header-grid">
    <a href="/index.php" class="brand" aria-label="SEMHYS">
      <img src="/assets/img/logo.svg" alt="Logo SEMHYS: ingeniería sostenible, agua y energía" title="SEMHYS Ingeniería Sostenible" style="width:48px;height:48px;border-radius:50%;background:#fff;box-shadow:0 2px 8px #0003;object-fit:cover;" itemprop="logo"/>
      <span class="brand-text">SEMHYS</span>
    </a>
    <nav class="nav" id="mainNav" aria-label="Navegación principal">
      <ul class="nav-list" style="display:flex;gap:2.2em;list-style:none;margin:0;padding:0;align-items:center;">
        <li><a href="/index.php" class="nav-link<?= $page==='home' ? ' active' : '' ?>">Inicio</a></li>
        <li><a href="/about.php" class="nav-link<?= $page==='about' ? ' active' : '' ?>">About</a></li>
  <li><a href="/services.php" class="nav-link<?= $page==='services' ? ' active' : '' ?>">Servicios</a></li>
  <li><a href="/blog.php" class="nav-link<?= $page==='blog' ? ' active' : '' ?>">Blog</a></li>
  <li><a href="/shop.php" class="nav-link<?= $page==='shop' ? ' active' : '' ?>">Tienda</a></li>
  <li><a href="/academy.php" class="nav-link<?= $page==='academy' ? ' active' : '' ?>">Academia</a></li>
  <li><a href="/contact.php" class="btn btn-primary" style="padding:10px 22px;font-size:1em;">Contacto</a></li>
      </ul>
    </nav>
    <div style="margin-left:auto;display:flex;align-items:center;gap:12px;">
      <select id="langSelect" aria-label="Seleccionar idioma" style="padding:0.3em 1em;border-radius:8px;font-size:1em;background:var(--surface);color:var(--text);border:1px solid #273246;box-shadow:0 2px 8px #0002;">
        <option value="es">Español</option>
        <option value="en">English</option>
        <option value="pt">Português</option>
        <option value="fr">Français</option>
        <option value="de">Deutsch</option>
      </select>
      <button class="nav-toggle" id="navToggle" aria-label="Abrir menú" aria-expanded="false" style="display:none;background:none;border:0;cursor:pointer;padding:8px;">
        <span style="display:block;width:28px;height:3px;background:var(--text);margin:6px 0;border-radius:2px;"></span>
        <span style="display:block;width:28px;height:3px;background:var(--text);margin:6px 0;border-radius:2px;"></span>
        <span style="display:block;width:28px;height:3px;background:var(--text);margin:6px 0;border-radius:2px;"></span>
      </button>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded',()=>{
      const navToggle = document.getElementById('navToggle');
      const navList = document.querySelector('.nav-list');
      navToggle && navToggle.addEventListener('click',()=>{ navList.classList.toggle('open'); });
      navList && navList.querySelectorAll('a').forEach(a=> a.addEventListener('click',()=> navList.classList.remove('open')));
    });
  </script>
</header>
