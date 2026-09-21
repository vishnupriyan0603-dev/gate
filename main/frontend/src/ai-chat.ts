import renderMathInElement from 'katex/contrib/auto-render';
import { Sparkles, Bot } from 'lucide';
import { api, el } from './api';
import { initIcons, toast } from './ui';
import { bindTips } from './fx';
import { renderRichMarkdown } from './md';

interface Subject {
  id: number;
  name: string;
}

// Floating "Ask GATE AI" assistant — loaded on first launcher click,
// wired to POST /api/ai/explain.
export function initAiChat(open = false): void {
  if (document.getElementById('ai-chat-root')) return;

  const root = el('div', { id: 'ai-chat-root' });

  // Reuse the zero-cost static launcher from layout.php when present.
  const staticLauncher = document.getElementById('ai-launcher-static');
  let launcher: HTMLElement;
  if (staticLauncher) {
    launcher = staticLauncher;
  } else {
    launcher = el('button', {
      id: 'ai-launcher',
      class:
        'anim-pulse-glow fixed bottom-5 right-5 z-[90] grid h-14 w-14 place-items-center rounded-2xl ' +
        'bg-gradient-to-br from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-2xl ' +
        'transition hover:scale-105 active:scale-95',
      'aria-label': 'Ask GATE AI',
      'data-tip': 'Ask GATE AI anything',
    });
    launcher.innerHTML = '<span data-lucide="sparkles" class="h-6 w-6"></span>';
    root.append(launcher);
  }

  const panel = el('div', {
    id: 'ai-panel',
    class:
      'fixed bottom-24 right-5 z-[91] hidden w-[min(92vw,24rem)] flex-col overflow-hidden rounded-2xl ' +
      'border border-violet-500/30 bg-[#131a32]/95 shadow-2xl backdrop-blur',
    style: 'max-height:min(70vh,32rem);',
  });

  const head = el('div', {
    class: 'flex items-center gap-2 bg-gradient-to-r from-violet-700 via-purple-600 to-fuchsia-600 px-4 py-3',
  });
  head.innerHTML = '<span data-lucide="bot" class="h-5 w-5 text-white"></span>';
  head.append(el('p', { class: 'flex-1 text-sm font-bold text-white' }, ['Ask GATE AI']));
  const closeBtn = el('button', { class: 'text-white/80 transition hover:text-white', 'aria-label': 'Close' }, ['✕']);
  head.append(closeBtn);
  panel.append(head);

  const subjRow = el('div', { class: 'border-b border-slate-800 px-4 py-2' });
  const subjSel = el('select', {
    id: 'ai-subject',
    class: 'select select-sm w-full bg-slate-800 text-slate-200',
  }) as HTMLSelectElement;
  subjSel.innerHTML = '<option value="">General (no subject)</option>';
  subjRow.append(subjSel);
  panel.append(subjRow);

  const msgs = el('div', { id: 'ai-msgs', class: 'flex-1 space-y-3 overflow-y-auto p-4' }) as HTMLElement;
  msgs.append(
    el('div', { class: 'anim-fade-up rounded-xl border border-slate-800 bg-slate-800/60 px-3 py-2' }, [
      renderRichMarkdown('Hi! Ask me any **GATE CS** concept, complexity, proof or PYQ approach.'),
    ]),
  );
  panel.append(msgs);

  const form = el('form', { id: 'ai-form', class: 'flex gap-2 border-t border-slate-800 p-3' }) as HTMLFormElement;
  const input = el('input', {
    id: 'ai-input',
    class: 'input input-sm input-bordered flex-1 bg-slate-800 text-slate-100',
    placeholder: 'e.g. Master theorem case 2…',
    autocomplete: 'off',
  }) as HTMLInputElement;
  const send = el('button', { class: 'btn btn-sm btn-primary btn-press', type: 'submit' }, ['➤']);
  form.append(input, send);
  panel.append(form);

  root.append(panel);
  document.body.append(root);
  void initIcons({ Sparkles, Bot });
  void bindTips(root);

  let subjectsLoaded = false;
  const ensureSubjects = () => {
    if (subjectsLoaded) return;
    subjectsLoaded = true;
    api<Subject[]>('/api/subjects')
      .then((rows) => {
        for (const s of rows) {
          const o = document.createElement('option');
          o.value = String(s.id);
          o.textContent = s.name;
          subjSel.append(o);
        }
        const m = new URLSearchParams(location.search).get('subject');
        if (m) subjSel.value = m;
      })
      .catch(() => {});
  };

  const toggle = (open?: boolean) => {
    const isHidden = panel.classList.contains('hidden');
    const show = open ?? isHidden;
    panel.classList.toggle('hidden', !show);
    panel.classList.toggle('flex', show);
    if (show) {
      panel.classList.remove('anim-scale-in');
      void panel.offsetWidth;
      panel.classList.add('anim-scale-in');
      ensureSubjects();
      window.setTimeout(() => input.focus(), 60);
    }
  };

  launcher.addEventListener('click', () => toggle());
  closeBtn.addEventListener('click', () => toggle(false));
  if (open) toggle(true);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !panel.classList.contains('hidden')) toggle(false);
  });

  const scrollBottom = () => {
    msgs.scrollTop = msgs.scrollHeight;
  };

  const addMsg = (role: 'user' | 'ai', html: string | Node): HTMLElement => {
    const wrap = el('div', {
      class: role === 'user' ? 'flex justify-end' : 'flex justify-start',
    });
    const bubble = el('div', {
      class:
        'anim-fade-up max-w-[88%] rounded-xl px-3 py-2 ' +
        (role === 'user'
          ? 'bg-indigo-600 text-sm text-white'
          : 'border border-slate-800 bg-slate-800/60'),
    });
    if (typeof html === 'string') bubble.innerHTML = html;
    else bubble.append(html);
    wrap.append(bubble);
    msgs.append(wrap);
    scrollBottom();
    return bubble;
  };

  const typing = (): HTMLElement => {
    const wrap = el('div', { class: 'flex justify-start', id: 'ai-typing' });
    const b = el('div', { class: 'flex gap-1 rounded-xl border border-slate-800 bg-slate-800/60 px-4 py-3' });
    for (let i = 0; i < 3; i++) {
      const d = el('span', { class: 'h-2 w-2 animate-bounce rounded-full bg-indigo-400' });
      d.style.animationDelay = `${i * 150}ms`;
      b.append(d);
    }
    wrap.append(b);
    msgs.append(wrap);
    scrollBottom();
    return wrap;
  };

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const q = input.value.trim();
    if (!q) return;
    input.value = '';
    const subjName = subjSel.selectedOptions[0]?.textContent || '';
    addMsg('user', el('span', {}, [q]));
    const t = typing();
    try {
      const prompt = subjSel.value && subjName ? `Context subject: ${subjName}. Question: ${q}` : q;
      const r = await api<{ explanation: string }>('/api/ai/explain', {
        method: 'POST',
        body: JSON.stringify({ text: prompt }),
      });
      t.remove();
      const bubble = addMsg('ai', renderRichMarkdown(r.explanation || ''));
      try {
        renderMathInElement(bubble, {
          delimiters: [
            { left: '$$', right: '$$', display: true },
            { left: '\\(', right: '\\)', display: false },
            { left: '$', right: '$', display: false },
          ],
          throwOnError: false,
        });
      } catch {
        /* math optional */
      }
      void bindTips(bubble);
    } catch (err) {
      t.remove();
      addMsg('ai', renderRichMarkdown(`**Error:** ${String(err)}`));
      toast(String(err), 'error');
    }
    scrollBottom();
  });
}
