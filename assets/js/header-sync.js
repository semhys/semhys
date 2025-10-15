// header-sync.js
// Unifica el logo y el selector de idioma en páginas estáticas.
document.addEventListener('DOMContentLoaded', () => {
  // Uniform logo inside .brand anchors
  document.querySelectorAll('a.brand').forEach(a => {
    try {
      // Preserve href and aria-label
      const href = a.getAttribute('href') || 'index.html#home';
      const aria = a.getAttribute('aria-label') || 'SEMHYS';
      a.innerHTML = '';
      a.setAttribute('href', href);
      a.setAttribute('aria-label', aria);
  const img = document.createElement('img');
  // Use absolute path on server, but relative path when opened via file:// for local testing
  const isFile = location.protocol === 'file:';
  img.src = isFile ? 'assets/img/logo.svg' : '/assets/img/logo.svg';
      img.alt = 'Logo SEMHYS: ingeniería sostenible, agua y energía';
      img.title = 'SEMHYS Ingeniería Sostenible';
      img.style.width = '48px'; img.style.height = '48px';
      img.style.borderRadius = '50%'; img.style.background = '#fff';
      img.style.objectFit = 'cover'; img.style.boxShadow = '0 2px 8px #0003';
      img.setAttribute('itemprop','logo');
      const span = document.createElement('span');
      span.className = 'brand-text';
      span.textContent = 'SEMHYS';
      a.appendChild(img);
      a.appendChild(span);
    } catch (e) { /* ignore */ }
  });

  // Ensure a single langSelect in header area
  try {
    // Remove any existing selects with id langSelect
    document.querySelectorAll('#langSelect').forEach(el => el.remove());

    // Build the selector
    const sel = document.createElement('select');
    sel.id = 'langSelect';
    sel.style.padding = '0.3em 1em';
    sel.style.borderRadius = '8px';
    sel.innerHTML = `
      <option value="es">Español</option>
      <option value="en">English</option>
      <option value="pt">Português</option>
      <option value="fr">Français</option>
      <option value="de">Deutsch</option>
    `;

    // Find header action area (a container at the right of header)
    let headerAction = document.querySelector('.header-grid') || document.querySelector('.site-header .container');
    if (headerAction) {
      // Create wrapper div similar to other pages
      const wrapper = document.createElement('div');
      wrapper.style.marginLeft = 'auto';
      wrapper.style.display = 'flex';
      wrapper.style.alignItems = 'center';
      wrapper.style.gap = '12px';
      wrapper.appendChild(sel);
      headerAction.appendChild(wrapper);
    } else {
      // Fallback: append to body
      document.body.appendChild(sel);
    }

    // Wire translations and persist selection in localStorage
    try {
      const KEY = 'semhys_lang';
      const stored = localStorage.getItem(KEY);
      if (stored) sel.value = stored;
      sel.addEventListener('change', e => {
        localStorage.setItem(KEY, e.target.value);
        if (typeof setLang === 'function') setLang(e.target.value);
      });
      if (typeof setLang === 'function') setLang(sel.value);
    } catch (e) { /* ignore storage errors */ }
  } catch (e) { /* ignore */ }

  // Ensure footer year element id 'year' exists and is set
  try {
    let yearEl = document.getElementById('year');
    if (!yearEl) {
      // If there's an element with id 'y' convert it
      const y = document.getElementById('y');
      if (y) { y.id = 'year'; yearEl = y; }
    }
    if (!yearEl) {
      // create one in first footer
      const f = document.querySelector('footer .container');
      if (f) {
        const sp = document.createElement('span'); sp.id = 'year'; sp.textContent = new Date().getFullYear();
        f.appendChild(sp);
      }
    } else {
      yearEl.textContent = new Date().getFullYear();
    }
  } catch (e) { /* ignore */ }
});
