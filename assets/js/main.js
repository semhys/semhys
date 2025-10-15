/* ===== utilidades ===== */
const $ = s => document.querySelector(s);
const $$ = s => document.querySelectorAll(s);

/* año en footer */
(() => { const y = new Date().getFullYear(); const el = document.getElementById('year'); if (el) el.textContent = y; })();

/* menú móvil */
(() => {
  const btn = $('.nav-toggle');
  const nav = $('#mainNav');
  if (!btn || !nav) return;
  btn.addEventListener('click', () => {
    const opened = nav.classList.toggle('open');
    btn.setAttribute('aria-expanded', opened ? 'true' : 'false');
  });
})();

/* Persistencia de idioma si existe el selector (cooperar con header-sync.js) */
(() => {
  const sel = document.getElementById('langSelect');
  if (!sel) return;
  try {
    const KEY = 'semhys_lang';
    const stored = localStorage.getItem(KEY);
    if (stored) sel.value = stored;
    sel.addEventListener('change', e => {
      localStorage.setItem(KEY, e.target.value);
      if (typeof setLang === 'function') setLang(e.target.value);
    });
    if (typeof setLang === 'function') setLang(sel.value);
  } catch (e) { /* ignore */ }
})();

/* ===== Chat conectado a n8n =====
   Pon aquí tu URL de Webhook (Production URL) del nodo Webhook en n8n */
const N8N_WEBHOOK_URL = 'https://TU_SUBDOMINIO.app.n8n.cloud/webhook/REEMPLAZA_ESTO'; // <-- CAMBIA ESTO

/* UI chat */
(() => {
  const bubble = $('#chatBubble');
  const panel  = $('#chatPanel');
  const close  = $('#chatClose');
  const form   = $('#chatForm');
  const input  = $('#chatText');
  const body   = $('#chatBody');

  if (!bubble || !panel || !form) return;

  function openChat() {
    panel.classList.add('active');
    panel.setAttribute('aria-hidden','false');
    input.focus();
  }
  function closeChat() {
    panel.classList.remove('active');
    panel.setAttribute('aria-hidden','true');
  }

  bubble.addEventListener('click', openChat);
  close && close.addEventListener('click', closeChat);

  async function sendToN8N(message) {
    const payload = {
      message,
      source: 'semhys.com',
      path: location.pathname,
      userAgent: navigator.userAgent
    };
    const res = await fetch(N8N_WEBHOOK_URL, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    // el nodo "Respond to Webhook" debe responder JSON: { reply: "..." }
    const data = await res.json().catch(()=> ({}));
    return data && (data.reply || data.message || data.answer || '');
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;

    // pinta mensaje usuario
    const u = document.createElement('div');
    u.className = 'msg msg--user';
    u.textContent = text;
    body.appendChild(u);
    body.scrollTop = body.scrollHeight;
    input.value = '';

    // placeholder bot (esqueleto)
    const b = document.createElement('div');
    b.className = 'msg msg--bot';
    b.textContent = 'Pensando…';
    body.appendChild(b);
    body.scrollTop = body.scrollHeight;

    try {
      const reply = await sendToN8N(text);
      b.textContent = reply ? reply : 'Gracias, hemos recibido tu mensaje.';
    } catch (err) {
      b.textContent = 'No se pudo conectar con el servidor. Intenta más tarde.';
    }
    body.scrollTop = body.scrollHeight;
  });
})();