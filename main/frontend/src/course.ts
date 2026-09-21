import katex from 'katex';
import renderMathInElement from 'katex/contrib/auto-render';
import { api, qs, qsa, el } from './api';
import { ring, toast, burst, celebrate } from './ui';
import { missionCheer } from './motivation';
import { pop, flash, shake } from './fx';

interface MiniMcq {
  id: number;
  q: string;
  a: string;
  b: string;
  c: string;
  d: string;
  k: string;
  x: string;
}

export function init(): void {
  initRings();
  initBars();
  renderDiagrams();
  renderMath();
  initTopicDone();
  initCourseQuiz();
}

function initRings(): void {
  qsa<HTMLElement>('.progress-ring').forEach((n) => {
    const p = Number(n.dataset.pct || '0');
    n.innerHTML = ring(p, 72);
  });
}

function initBars(): void {
  qsa<HTMLElement>('.bar-fill').forEach((b) => {
    const w = b.dataset.w || '0';
    requestAnimationFrame(() => requestAnimationFrame(() => {
      b.style.width = `${w}%`;
    }));
  });
}

async function renderDiagrams(): Promise<void> {
  const elm = qs<HTMLElement>('.mermaid');
  if (!elm) return;
  try {
    const { default: mermaid } = await import('mermaid');
    mermaid.initialize({ startOnLoad: false, theme: 'dark' });
    await mermaid.run();
  } catch {
    /* diagrams are decorative */
  }
}

function renderMath(): void {
  renderMathInElement(document.body, {
    delimiters: [
      { left: '$$', right: '$$', display: true },
      { left: '\\(', right: '\\)', display: false },
      { left: '$', right: '$', display: false },
    ],
    throwOnError: false,
  });
}

function initTopicDone(): void {
  const btn = qs<HTMLButtonElement>('#btn-topic-done');
  if (!btn) return;
  btn.addEventListener('click', async () => {
    pop(btn);
    try {
      await api(`/api/topics/${btn.dataset.id}/status`, {
        method: 'POST',
        body: JSON.stringify({ status: 'done' }),
      });
      btn.textContent = 'Completed ✓';
      btn.classList.add('btn-success');
      btn.classList.remove('btn-primary');
      missionCheer('Topic complete · +20 XP');
    } catch (e) {
      toast(String(e), 'error');
    }
  });
}

function initCourseQuiz(): void {
  const host = qs<HTMLElement>('#course-quiz');
  if (!host) return;
  const raw = host.dataset.mcqs;
  if (!raw) return;
  let bank: MiniMcq[] = [];
  try {
    bank = JSON.parse(raw);
  } catch {
    host.innerHTML = '<p class="text-sm text-slate-500">Could not load questions for this topic.</p>';
    return;
  }
  if (bank.length === 0) {
    host.innerHTML = '<p class="text-sm text-slate-500">No practice questions available for this topic yet.</p>';
    return;
  }

  let idx = 0;
  let correct = 0;
  let answered = 0;

  const result = (r: { xp: number }) => {
    void r;
  };

  const submit = async () => {
    try {
      const r = await api<{ xp: number }>('/api/quiz/result', {
        method: 'POST',
        body: JSON.stringify({ type: 'quick', total: bank.length, correct, attempted: answered, seconds: 0 }),
      });
      result(r);
      toast(`Course practice: ${correct}/${bank.length} · +${r.xp} XP`, 'success');
      host.innerHTML = '';
    } catch {
      toast('Could not save result', 'info');
    }
  };

  const render = () => {
    const m = bank[idx];
    if (!m) {
      host.append(el('p', { class: 'anim-scale-in text-sm text-slate-300 font-medium' }, [`Practice complete: ${correct}/${bank.length} correct`]));
      celebrate(bank.length ? Math.round((correct / bank.length) * 100) : 0);
      submit();
      return;
    }
    host.innerHTML = '';
    host.append(el('p', { class: 'text-xs text-slate-500' }, [`Question ${idx + 1} / ${bank.length}`]));
    host.append(el('p', { class: 'anim-slide-in mt-2 font-semibold text-slate-100' }, [m.q]));

    const opts: [string, string][] = [
      ['A', m.a], ['B', m.b], ['C', m.c], ['D', m.d],
    ];
    const wrap = el('div', { class: 'mt-4 space-y-2.5' });
    for (const [letter, text] of opts) {
      const btn = el('button', {
        class: 'quiz-opt group',
      });
      const badge = el('span', {
        class: 'key',
      }, [letter]);
      const content = el('span', { class: 'flex-1' }, [text]);
      btn.append(badge, content);

      btn.addEventListener('click', () => {
        answered++;
        const ok = letter.toLowerCase() === m.k.toLowerCase();
        if (ok) correct++;
        btn.className = `quiz-opt ${ok ? 'quiz-opt-correct' : 'quiz-opt-wrong'}`;
        badge.className = 'key';
        if (ok) {
          pop(btn);
          flash(btn, true);
        } else {
          shake(btn);
          flash(btn, false);
        }
        const fb = el('div', {
          class: `anim-fade-up mt-3 p-3 text-xs ${ok ? 'feedback-ok' : 'feedback-err'}`,
        }, [
          el('p', { class: 'font-bold' }, [ok ? `✓ Correct Answer (${m.k})` : `✗ Incorrect — Correct answer is (${m.k})`]),
          m.x ? el('p', { class: 'mt-1 opacity-90 leading-relaxed' }, [m.x]) : '',
        ]);
        wrap.append(fb);
        qsa<HTMLButtonElement>('button', wrap).forEach((b) => (b.disabled = true));
        const next = el('button', {
          class: 'btn btn-sm btn-quest btn-press mt-3 inline-flex items-center gap-2 rounded-xl',
        }, [idx === bank.length - 1 ? 'Complete Practice ✓' : 'Next Question →']);
        next.addEventListener('click', () => {
          idx++;
          render();
        });
        wrap.append(next);
      });
      wrap.append(btn);
    }
    host.append(wrap);
  };

  render();
}
