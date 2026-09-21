const DEFAULT_URL = 'http://localhost/gate%20exam%20preparation/public';

let appUrl = DEFAULT_URL;

async function init() {
  const { appUrl: saved } = await chrome.storage.sync.get({ appUrl: DEFAULT_URL });
  appUrl = (saved || DEFAULT_URL).replace(/\/+$/, '');
  document.getElementById('link-app').href = appUrl;
  try {
    await refreshStatus();
  } catch { /* ignore */ }
  document.getElementById('btn-latest').addEventListener('click', loadLatest);
  document.getElementById('btn-quiz').addEventListener('click', loadQuiz);
}

async function callApp(path, opts) {
  const res = await fetch(appUrl + path, {
    headers: { 'Content-Type': 'application/json' },
    ...opts,
  });
  let data = null;
  try { data = await res.json(); } catch { /* ignore */ }
  if (!res.ok) throw new Error((data && data.message) || `HTTP ${res.status}`);
  return data;
}

function setStatus(text, ok) {
  const el = document.getElementById('status');
  el.textContent = text;
  el.style.color = ok === false ? '#f87171' : ok ? '#6ee7b7' : '#94a3b8';
}

function levelTag(lbl) {
  const cls = lbl === 'Weak' ? 'weak' : lbl === 'Strong' ? 'strong' : 'med';
  return `<span class="tag ${cls}">${lbl}</span>`;
}

function mcqCard(m, { revealable = false } = {}) {
  const opts = ['A', 'B', 'C', 'D'].map((L) => {
    const text = m['opt_' + L.toLowerCase()];
    const isKey = m.answer === L;
    return `<div style="padding:2px 0">${L}) ${text} ${isKey ? '<span style="color:#10b981">✔</span>' : ''}</div>`;
  }).join('');
  return `<div class="mcq"><div class="q">${m.question}</div>${opts}<div class="meta">${m.subject_name || ''} · ${m.difficulty || ''}</div></div>`;
}

async function refreshStatus() {
  try {
    const lv = await callApp('/api/ai/study-level');
    const subj = lv.subjects || [];
    const html = subj.map((s) => `<span class="tag ${s.level === 'E' ? 'weak' : s.level === 'H' ? 'strong' : 'med'}">${s.name}: ${s.label}</span>`).join(' ');
    setStatus('Study levels: ' + html);
    return lv;
  } catch (e) {
    setStatus('⚠ Cannot reach trainer app: ' + e.message + ' — open Options to fix the URL.', false);
    return null;
  }
}

async function loadLatest() {
  const btn = document.getElementById('btn-latest');
  btn.disabled = true;
  btn.textContent = 'Generating… (uses Groq)';
  const results = document.getElementById('results');
  try {
    const d = await callApp('/api/ai/latest');
    let html = '';
    if (d.focus_subject) {
      html += `<div class="mini" style="color:#fcd34d">Focus: ${d.focus_subject.name} (${d.focus_subject.label}, ${d.focus_subject.accuracy == null ? 'no data yet' : d.focus_subject.accuracy + '% accuracy'})</div>`;
    }
    if (d.generated && d.generated.mcqs && d.generated.mcqs.length) {
      html += `<div class="mini">Fresh AI MCQs (${d.generated.difficulty} · ${d.generated.model}):</div>`;
      html += d.generated.mcqs.map((m) => mcqCard(m, { revealable: true })).join('');
    } else {
      html += '<div class="mini">No Groq key configured — add it in the web app Settings → AI.</div>';
    }
    if (d.recent && d.recent.length) {
      html += '<div class="mini">Recent from my bank:</div>';
      html += d.recent.map((m) => mcqCard(m, { revealable: true })).join('');
    }
    results.innerHTML = html || '<div class="mini">Nothing yet.</div>';
    setStatus('Done — adaptively matched to your study level.', true);
  } catch (e) {
    setStatus('⚠ ' + e.message, false);
  } finally {
    btn.disabled = false;
    btn.textContent = '⚡ Latest MCQs (my weakest subject)';
  }
}

async function loadQuiz() {
  const btn = document.getElementById('btn-quiz');
  btn.disabled = true;
  btn.textContent = 'Preparing…';
  const results = document.getElementById('results');
  try {
    const lv = await callApp('/api/ai/study-level');
    const subject = (lv.subjects && lv.subjects.find((s) => s.level === 'E')) || lv.subjects[0];
    if (!subject) throw new Error('No subjects found');
    const d = await callApp('/api/ai/generate', {
      method: 'POST',
      body: JSON.stringify({ subject_id: subject.id, count: 5, difficulty: 'auto' }),
    });
    results.innerHTML =
      `<div class="mini">Gut check on <b>${subject.name}</b> (${d.subject_level === 'H' ? 'Strong' : d.subject_level === 'E' ? 'Weak' : 'Medium'}):</div>` +
      d.mcqs.map((m) => {
        const opts = ['A', 'B', 'C', 'D'].map((L) => `<div style="padding:2px 0">${L}) ${m['opt_' + L.toLowerCase()]}</div>`).join('');
        return `<div class="mcq"><div class="q">${m.question}</div>${opts}<button class="reveal" style="margin-top:6px;padding:3px 8px;background:#334155;color:#e2e8f0;border:0;border-radius:6px;cursor:pointer">Reveal answer</button><div class="reveal-body" style="display:none;margin-top:6px;color:#6ee7b7">Answer: ${m.answer} — ${m.explanation || ''}</div></div>`;
      }).join('');
    results.querySelectorAll('.reveal').forEach((b) => b.addEventListener('click', () => {
      const body = b.nextElementSibling;
      body.style.display = body.style.display === 'none' ? 'block' : 'none';
    }));
    setStatus(`Quiz ready — ${new Date().toLocaleTimeString()}`, true);
  } catch (e) {
    setStatus('⚠ ' + e.message, false);
  } finally {
    btn.disabled = false;
    btn.textContent = '🎯 Quick 5-question gut check';
  }
}

init();