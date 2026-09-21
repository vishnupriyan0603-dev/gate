const DEFAULT_URL = 'http://localhost/gate%20exam%20preparation/public';

const urlInput = document.getElementById('appUrl');
const modelInput = document.getElementById('groqModel');
const msg = document.getElementById('msg');

chrome.storage.sync.get({ appUrl: DEFAULT_URL }, (data) => {
  urlInput.value = data.appUrl;
});

async function fetchModel() {
  try {
    const base = (urlInput.value || DEFAULT_URL).replace(/\/+$/, '');
    const res = await fetch(base + '/api/ai/status');
    const j = await res.json();
    modelInput.value = j.configured ? j.model : '(no Groq key configured yet)';
  } catch {
    modelInput.value = '(app unreachable — is XAMPP running?)';
  }
}

urlInput.addEventListener('change', fetchModel);
fetchModel();

document.getElementById('save').addEventListener('click', () => {
  const url = urlInput.value.trim() || DEFAULT_URL;
  chrome.storage.sync.set({ appUrl: url }, () => {
    msg.textContent = 'Saved. Reload the extension popup.';
    msg.className = 'ok';
    fetchModel();
  });
});