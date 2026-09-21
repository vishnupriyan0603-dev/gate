const DEFAULT_URL = 'http://localhost/gate%20exam%20preparation/public';

async function appUrl() {
  const { appUrl } = await chrome.storage.sync.get({ appUrl: DEFAULT_URL });
  return (appUrl || DEFAULT_URL).replace(/\/+$/, '');
}

async function callApp(path, opts) {
  const base = await appUrl();
  const res = await fetch(base + path, {
    headers: { 'Content-Type': 'application/json' },
    ...opts,
  });
  let data = null;
  try { data = await res.json(); } catch (e) { /* ignore */ }
  if (!res.ok) throw new Error((data && data.message) || ('HTTP ' + res.status));
  return data;
}

chrome.runtime.onInstalled.addListener(() => {
  chrome.contextMenus.create({
    id: 'gate-explain',
    title: '🤖 Explain with GATE AI',
    contexts: ['selection'],
  });
});

chrome.contextMenus.onClicked.addListener((info, tab) => {
  if (info.menuItemId === 'gate-explain' && info.selectionText && tab && tab.id != null) {
    const tabId = tab.id;
    const sel = info.selectionText;
    callApp('/api/ai/explain', {
      method: 'POST',
      body: JSON.stringify({ text: sel }),
    })
      .then((d) => {
        chrome.tabs.sendMessage(tabId, { type: 'gate-explain', selection: sel, explanation: d.explanation });
      })
      .catch((e) => {
        chrome.tabs.sendMessage(tabId, { type: 'gate-error', message: String(e) });
      });
  }
});

chrome.runtime.onMessage.addListener((msg, _sender, sendResponse) => {
  if (msg && msg.type === 'gate-explain-selection') {
    callApp('/api/ai/explain', {
      method: 'POST',
      body: JSON.stringify({ text: msg.text }),
    })
      .then((d) => sendResponse({ ok: true, explanation: d.explanation }))
      .catch((e) => sendResponse({ ok: false, message: String(e) }));
    return true; // async response
  }
  return false;
});