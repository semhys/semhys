(function(){
  const CHAT_URL = 'https://TU-SUBDOMINIO.n8n.cloud/chat/<ID_DEL_CHAT>'; // TODO: pon aquí tu URL de chat n8n (Hosted/Embedded)
  const WEBHOOK_URL = 'https://semhys.app.n8n.cloud/webhook/chat_semhys'; // TODO (opcional) si también quieres enviar eventos a un webhook

  function q(s){return document.querySelector(s)}
  function ce(t){return document.createElement(t)}

  // FAB
  const fab = ce('div'); fab.className='chat-fab'; fab.title='Chat SEMHYS'; fab.innerHTML='💬'
  document.body.appendChild(fab)

  // Modal
  const modal = ce('div'); modal.className='chat-modal'
  modal.innerHTML = `
    <div class="chat-panel">
      <div class="chat-head"><strong>Asistente SEMHYS</strong>
        <button id="chatClose" class="btn ghost" style="padding:6px 10px">Cerrar</button>
      </div>
      <iframe class="chat-iframe" src="${CHAT_URL}" referrerpolicy="no-referrer"></iframe>
    </div>`
  document.body.appendChild(modal)

  q('.chat-fab').onclick = ()=>{ modal.style.display='flex'; sendEvent('open_chat') }
  q('#chatClose').onclick = ()=>{ modal.style.display='none'; sendEvent('close_chat') }

  // (Opcional) telemetría mínima hacia n8n webhook
  function sendEvent(type){
    if(!WEBHOOK_URL) return;
    const payload = {
      type,
      page: location.pathname,
      title: document.title,
      tz: Intl.DateTimeFormat().resolvedOptions().timeZone,
      ua: navigator.userAgent
    };
    fetch(WEBHOOK_URL,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)}).catch(()=>{})
  }
  sendEvent('page_view')
})();