let panel = null;
let lastMouse = { x: 16, y: 16 };

function showPanel(html, title) {
  hidePanel();
  panel = document.createElement('div');
  panel.id = 'gate-ai-panel';
  panel.style.cssText =
    'position:fixed;right:16px;top:16px;z-index:2147483647;width:380px;max-width:92vw;max-height:80vh;overflow:auto;' +
    'background:#0f172a;color:#e2e8f0;border:1px solid #334155;border-radius:12px;padding:16px;' +
    'font:14px/1.5 system-ui,sans-serif;box-shadow:0 12px 40px rgba(0,0,0,.5);';
  const head = document.createElement('div');
  head.style.cssText = 'display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;font-weight:600;color:#a5b4fc;';
  head.textContent = title;
  const close = document.createElement('button');
  close.textContent = '✕';
  close.style.cssText = 'background:none;border:0;color:#94a3b8;cursor:pointer;font-size:14px;';
  close.onclick = hidePanel;
  head.append(close);
  const body = document.createElement('div');
  body.innerHTML = html;
  panel.append(head, body);
  document.documentElement.appendChild(panel);
}

function hidePanel() {
  if (panel) { panel.remove(); panel = null; }
}

chrome.runtime.onMessage.addListener((msg) => {
  if (!msg || !msg.type) return;
  if (msg.type === 'gate-explain') {
    const sel = msg.selection || '';
    const quoted = sel.length > 160 ? sel.slice(0, 160) + '…' : sel;
    showPanel(
      '<div style="color:#64748b;font-size:12px;margin-bottom:8px;">“' + quoted.replace(/</g, '&lt;') + '”</div><div>' + msg.explanation.replace(/\n/g, '<br>') + '</div>',
      '🤖 GATE AI · Explanation'
    );
  } else if (msg.type === 'gate-error') {
    showPanel('<div style="color:#f87171">⚠ ' + String(msg.message).replace(/</g, '&lt;') + '</div>', 'GATE AI');
  }
});

// Selection → mini "Ask GATE AI" button anchored at the cursor
document.addEventListener('mouseup', (e) => {
  lastMouse = { x: e.clientX, y: e.clientY };
  const gs = window.getSelection();
  const sel = gs ? gs.toString().trim() : '';
  document.getElementById('gate-ai-ask')?.remove();
  if (!sel || sel.length < 10) return;
  const button = document.createElement('div');
  button.id = 'gate-ai-ask';
  button.textContent = '🤖 Ask GATE AI';
  button.style.cssText =
    'position:fixed;z-index:2147483646;background:#4f46e5;color:#fff;border:0;border-radius:8px;' +
    'padding:6px 10px;font:13px system-ui,sans-serif;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,.35);' +
    'left:' + Math.min(window.innerWidth - 160, e.clientX + 8) + 'px;top:' + (e.clientY + 12) + 'px;';
  button.onclick = () => {
    button.remove();
    showPanel('<div style="color:#94a3b8">Thinking…</div>', '🤖 GATE AI');
    chrome.runtime.sendMessage({ type: 'gate-explain-selection', text: sel }, (res) => {
      hidePanel();
      if (res && res.ok) {
        showPanel('<div>' + res.explanation.replace(/\n/g, '<br>') + '</div>', '🤖 GATE AI · Explanation');
      } else {
        showPanel('<div style="color:#f87171">⚠ ' + String(res && res.message).replace(/</g, '&lt;') + '</div>', 'GATE AI');
      }
    });
  };
  document.documentElement.appendChild(button);
});

document.addEventListener('mousedown', () => {
  const b = document.getElementById('gate-ai-ask');
  if (b) b.remove();
});