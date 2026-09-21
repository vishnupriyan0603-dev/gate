import renderMathInElement from 'katex/contrib/auto-render';
import { Crosshair } from 'lucide';
import { api, qs, qsa, el } from './api';
import { toast, celebrate, initIcons } from './ui';
import { pop, shake, flash, isTyping, bindTips } from './fx';

interface Mcq {
  id: number;
  subject_id: number;
  subject_name?: string;
  phase_id?: number | null;
  question: string;
  opt_a: string;
  opt_b: string;
  opt_c: string;
  opt_d: string;
  answer: string;
  explanation?: string;
  difficulty?: string;
}

interface Subject {
  id: number;
  name: string;
}

let subjects: Subject[] = [];

export function init(): void {
  void initIcons({ Crosshair });
  api<Subject[]>('/api/subjects').then((s) => (subjects = s)).catch(() => {});
  initQuizModes();
  initChallenge();
  initManager();
}

function fmtTime(t: number): string {
  const m = Math.floor(t / 60);
  const s = t % 60;
  return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
}

// ==================== QUIZ ENGINE ====================

const MODE_COUNT: Record<string, number> = {
  quick: 10,
  timed: 15,
  random10: 10,
  random25: 25,
  fullmock: 65,
  weak: 15,
  challenge: 10,
};

const MODE_TIME: Record<string, number> = {
  timed: 120,
  random10: 300,
  random25: 600,
  fullmock: 1800,
  challenge: 600,
};

// Engine currently on screen — drives keyboard shortcuts (1–4 answer, Enter next).
let activeEngine: QuizEngine | null = null;
let keysBound = false;

function initQuizKeys(): void {
  if (keysBound) return;
  keysBound = true;
  document.addEventListener('keydown', (e) => {
    if (!activeEngine || isTyping()) return;
    if (e.key >= '1' && e.key <= '4') {
      e.preventDefault();
      activeEngine.pressOption(Number(e.key) - 1);
    } else if (e.key === 'Enter') {
      const ae = activeEngine;
      if (ae) {
        e.preventDefault();
        ae.pressNext();
      }
    }
  });
}

class QuizEngine {
  private bank: Mcq[] = [];
  private idx = 0;
  private correct = 0;
  private answered = 0;
  private startSec = Math.floor(Date.now() / 1000);
  private timerId: ReturnType<typeof setInterval> | null = null;
  private secondsLeft: number;
  private optBtns: HTMLButtonElement[] = [];
  private nextBtn: HTMLButtonElement | null = null;

  constructor(
    private mode: string,
    private subjectId: number,
    private host: HTMLElement,
    private phaseId: number = 0,
  ) {
    this.secondsLeft = MODE_TIME[mode] ?? 0;
  }

  async start(): Promise<void> {
    activeEngine = this;
    this.host.classList.remove('anim-slide-in');
    void this.host.offsetWidth;
    this.host.classList.add('anim-slide-in');
    window.setTimeout(() => this.host.classList.remove('anim-slide-in'), 400);
    const count = MODE_COUNT[this.mode];
    const q = new URLSearchParams();
    if (this.subjectId) q.set('subject', String(this.subjectId));
    if (this.phaseId) q.set('phase', String(this.phaseId));
    q.set('count', String(count));
    this.bank = await api<Mcq[]>(`/api/mcqs?${q.toString()}`);
    if (this.bank.length === 0) {
      this.host.innerHTML = `
        <div class="text-center py-12">
          <p class="text-slate-400">No questions found in bank for this filter.</p>
          ${this.phaseId ? '<p class="mt-1 text-xs text-slate-500">This phase has no tagged questions yet — generate some with 🤖 AI below.</p>' : ''}
          <button id="btn-quiz-retry" class="btn btn-sm btn-primary mt-4">Try another filter</button>
        </div>
      `;
      qs<HTMLButtonElement>('#btn-quiz-retry')?.addEventListener('click', () => {
        location.reload();
      });
      return;
    }
    this.bank = this.bank.slice(0, count);
    if (this.secondsLeft > 0) {
      this.timerId = setInterval(() => {
        this.secondsLeft--;
        const t = qs<HTMLElement>('#quiz-timer');
        if (t) {
          t.textContent = this.mode === 'challenge' ? `🏆 ⏱ ${fmtTime(Math.max(0, this.secondsLeft))}` : `⏱ ${fmtTime(Math.max(0, this.secondsLeft))}`;
          t.classList.toggle('timer-urgent', this.secondsLeft <= 30 && this.secondsLeft > 0);
        }
        const bar = qs<HTMLElement>('#quiz-timebar');
        if (bar) {
          bar.style.width = `${Math.max(0, (this.secondsLeft / (MODE_TIME[this.mode] || 1)) * 100)}%`;
          const urgent = this.secondsLeft <= 30 && this.secondsLeft > 0;
          bar.style.background = urgent ? '#f43f5e' : '';
        }
        if (this.secondsLeft <= 0) {
          if (this.timerId) clearInterval(this.timerId);
          this.finish();
        }
      }, 1000);
    }
    this.render();
  }

  private render(): void {
    const m = this.bank[this.idx];
    if (!m) {
      this.finish();
      return;
    }
    this.host.innerHTML = '';
    const head = el('div', { class: 'flex items-center justify-between gap-3 text-sm' }, [
      el('p', { class: 'text-slate-400' }, [`${this.mode} · ${this.idx + 1} / ${this.bank.length}`]),
      el('p', { id: 'quiz-timer', class: 'text-indigo-300 font-semibold tabular-nums' }, [this.mode === 'challenge' ? `🏆 ⏱ ${fmtTime(this.secondsLeft)}` : `⏱ ${fmtTime(this.secondsLeft)}`]),
    ]);
    this.host.append(head);
    const bar = el('div', { class: 'mt-2 h-1.5 w-full rounded-full bg-slate-800' });
    bar.append(el('div', { id: 'quiz-timebar', class: 'h-1.5 rounded-full bg-indigo-500', style: 'width:100%' }));
    this.host.append(bar);

    const qCard = el('div', { class: 'anim-slide-in mt-4 rounded-xl border border-white/10 bg-black/30 p-4 study-card' });
    qCard.append(el('p', { class: 'text-lg font-semibold text-slate-100' }, [m.question]));
    if (m.subject_name) qCard.append(el('p', { class: 'mt-1 text-xs text-slate-500' }, [`📚 ${m.subject_name} · ${m.difficulty || 'M'}`]));
    this.host.append(qCard);

    const opts = el('div', { class: 'anim-slide-in mt-4 space-y-2.5' });
    opts.style.animationDelay = '70ms';
    const entries: [string, string, string][] = [
      ['A', m.opt_a, '1'], ['B', m.opt_b, '2'], ['C', m.opt_c, '3'], ['D', m.opt_d, '4'],
    ];
    this.optBtns = [];
    for (const [letter, text, keyHint] of entries) {
      const btn = el('button', {
        class: 'quiz-opt group',
      });
      const badge = el('span', {
        class: 'key',
      }, [letter]);
      badge.setAttribute('data-depth', '');
      const content = el('span', { class: 'flex-1 text-slate-100' }, [text]);
      const shortcut = el('span', {
        class: 'hidden sm:inline-flex items-center text-[10px] font-mono text-slate-500 bg-black/40 px-1.5 py-0.5 rounded border border-white/10 ml-2 group-hover:text-slate-300 transition-colors',
      }, [`[${keyHint}]`]);

      btn.append(badge, content, shortcut);
      btn.addEventListener('click', () => this.check(letter, btn, badge, opts));
      opts.append(btn);
      this.optBtns.push(btn);
    }
    this.nextBtn = null;
    this.host.append(opts);
    renderMathInElement(this.host, { delimiters: [{ left: '\\(', right: '\\)', display: false }, { left: '$', right: '$', display: false }], throwOnError: false });
  }

  private check(letter: string, btn: HTMLButtonElement, badge: HTMLElement, wrap: HTMLElement): void {
    const m = this.bank[this.idx];
    this.answered++;
    const ok = letter.toLowerCase() === m.answer.toLowerCase();
    if (ok) this.correct++;
    btn.className = `quiz-opt ${ok ? 'quiz-opt-correct' : 'quiz-opt-wrong'}`;
    badge.className = 'key';
    if (ok) {
      pop(btn);
      flash(btn, true);
    } else {
      shake(btn);
      flash(btn, false);
    }
    qsa<HTMLButtonElement>('button', wrap).forEach((b) => (b.disabled = true));

    const fb = el('div', {
      class: `anim-fade-up mt-3 p-3.5 text-xs ${ok ? 'feedback-ok' : 'feedback-err'}`,
    });
    fb.append(el('p', { class: 'font-bold' }, [ok ? `✓ Correct Answer (${m.answer}) — +10 XP` : `✗ Incorrect — Correct answer is (${m.answer})`]));
    if (m.explanation) fb.append(el('p', { class: 'mt-1 opacity-90 leading-relaxed font-sans' }, ['💡 ' + m.explanation]));
    wrap.append(fb);

    const next = el('button', {
      class: `btn btn-sm btn-press mt-3 inline-flex items-center gap-2 rounded-xl ${ok ? 'bg-emerald-600 hover:bg-emerald-500 text-white' : 'btn-quest'}`,
    }, [
      el('span', {}, [this.idx === this.bank.length - 1 ? 'Finish Practice' : 'Next Question']),
      el('span', { class: 'font-mono text-[10px] opacity-75 bg-black/20 px-1 py-0.5 rounded' }, ['[Enter]']),
    ]);
    next.addEventListener('click', () => {
      this.idx++;
      this.render();
    });
    wrap.append(next);
    this.nextBtn = next;
  }

  /** Keyboard: 1–4 pick an option, Enter advances. Called from the global key handler. */
  pressOption(i: number): void {
    const b = this.optBtns[i];
    if (b && !b.disabled) b.click();
  }

  pressNext(): void {
    if (this.nextBtn && !this.nextBtn.disabled) this.nextBtn.click();
  }

  private finish(): void {
    if (this.timerId) clearInterval(this.timerId);
    activeEngine = null;
    const elapsed = Math.floor(Date.now() / 1000) - this.startSec;
    const accuracy = this.bank.length ? Math.round((this.correct / this.bank.length) * 100) : 0;

    api<{ xp: number; total_xp: number }>('/api/quiz/result', {
      method: 'POST',
      body: JSON.stringify({
        type: this.mode,
        subject_id: this.subjectId,
        total: this.bank.length,
        correct: this.correct,
        attempted: this.answered,
        seconds: elapsed,
      }),
    })
      .then((r) => {
        this.host.innerHTML = '';
        const card = el('div', { class: 'anim-scale-in mx-auto max-w-md study-card rounded-2xl p-8 text-center' });
        card.append(el('p', { class: 'anim-float text-5xl mb-2' }, [accuracy >= 80 ? '🏆' : accuracy >= 50 ? '🎯' : '💪']));
        card.append(el('h2', { class: 'font-display text-2xl font-extrabold text-white tracking-tight' }, [`${this.correct} / ${this.bank.length} Correct`]));
        const badgesRow = el('div', { class: 'mt-3 flex flex-wrap items-center justify-center gap-2' });
        badgesRow.append(
          el('span', { class: `chip ${accuracy >= 70 ? 'chip-focus' : accuracy >= 50 ? 'chip-xp' : 'chip-streak'} text-xs font-mono font-bold` }, [`${accuracy}% Accuracy`]),
          el('span', { class: 'chip chip-quest text-xs font-mono' }, [`⏱ ${fmtTime(elapsed)}`]),
          el('span', { class: 'chip chip-xp text-xs font-mono font-bold level-up' }, [`+${r.xp} XP`]),
        );
        card.append(badgesRow);
        const btn = el('button', { class: 'btn btn-press btn-quest mt-6 w-full max-w-xs mx-auto rounded-xl' }, ['Practice Again']);
        btn.addEventListener('click', () => { this.reset(); this.start(); });
        card.append(btn);
        this.host.append(card);
        toast(`+${r.xp} XP earned`, 'success');
        celebrate(accuracy);
      })
      .catch((e) => toast(String(e), 'error'));
  }

  private reset(): void {
    this.idx = 0;
    this.correct = 0;
    this.answered = 0;
    this.startSec = Math.floor(Date.now() / 1000);
    this.secondsLeft = MODE_TIME[this.mode] ?? 0;
    this.bank = [];
  }
}

function initQuizModes(): void {
  const host = qs<HTMLElement>('#quiz-stage');
  if (!host) return;
  initQuizKeys();
  void bindTips(host);
  const subjectSel = qs<HTMLSelectElement>('#quiz-subject');
  const phaseSel = qs<HTMLSelectElement>('#quiz-phase');
  qsa<HTMLButtonElement>('.mode-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      pop(btn);
      qsa<HTMLButtonElement>('.mode-btn').forEach((b) => b.classList.remove('mode-btn-active', 'btn-primary'));
      qsa<HTMLButtonElement>('.mode-btn').forEach((b) => b.classList.add('btn-outline'));
      btn.classList.add('mode-btn-active');
      btn.classList.remove('btn-outline');
      const mode = btn.dataset.mode || 'quick';
      new QuizEngine(mode, Number(subjectSel?.value || 0), host, Number(phaseSel?.value || 0)).start().catch((e) => toast(String(e), 'error'));
    });
  });
}

function initChallenge(): void {
  const startBtn = qs<HTMLButtonElement>('#challenge-start');
  const stage = qs<HTMLElement>('#challenge-stage');
  const subjectSel = qs<HTMLSelectElement>('#challenge-subject');
  const phaseSel = qs<HTMLSelectElement>('#challenge-phase');
  if (!startBtn || !stage) return;
  initQuizKeys();
  startBtn.addEventListener('click', () => {
    pop(startBtn);
    startBtn.style.display = 'none';
    new QuizEngine('challenge', Number(subjectSel?.value || 0), stage, Number(phaseSel?.value || 0)).start().catch((e) => toast(String(e), 'error'));
  });
}

// ==================== MCQ MANAGER ====================

function initManager(): void {
  const list = qs<HTMLElement>('#mcq-list');
  const reload = qs<HTMLButtonElement>('#btn-reload-list');
  if (!list || !reload) return;

  const render = () => {
    api<Mcq[]>('/api/mcqs').then((rows) => {
      void subjects;
      list.innerHTML = '';
      const count = qs<HTMLElement>('#mcq-list-count');
      if (count) count.textContent = `· ${rows.length}`;
      for (const m of rows) {
        const card = el('div', { class: 'anim-fade-up study-card flex items-start justify-between gap-3 p-3' });
        card.style.setProperty('--d', `${Math.min(rows.indexOf(m), 11) * 35}ms`);
        const left = el('div', { class: 'min-w-0 flex-1' });
        left.append(el('p', { class: 'text-sm text-slate-200' }, [m.question]));
        left.append(el('p', { class: 'mt-0.5 text-xs text-slate-500' }, [`${m.subject_name || '?'} · ${m.difficulty || 'M'} · ${m.answer}`]));
        card.append(left);
        const actions = el('div', { class: 'flex shrink-0 gap-2' });
        const edit = el('button', { class: 'btn btn-xs btn-ghost' }, ['✎']);
        edit.addEventListener('click', () => openMcqDialog(m));
        const del = el('button', { class: 'btn btn-xs btn-ghost text-rose-400' }, ['🗑']);
        del.addEventListener('click', async () => {
          if (!confirm(`Delete: ${m.question.slice(0, 60)}…`)) return;
          await api(`/api/mcqs/${m.id}`, { method: 'DELETE' });
          render();
        });
        actions.append(edit, del);
        card.append(actions);
        list.append(card);
      }
    }).catch(() => {});
  };

  render();
  reload.addEventListener('click', render);

  const newBtn = qs<HTMLButtonElement>('#btn-new-mcq');
  if (newBtn) newBtn.addEventListener('click', () => openMcqDialog(null));

  const bulkBtn = qs<HTMLButtonElement>('#btn-bulk-mcq');
  if (bulkBtn) bulkBtn.addEventListener('click', openBulkDialog);
}

function openMcqDialog(m: Mcq | null): void {
  const dialog = qs<HTMLDialogElement>('#mcq-dialog');
  const form = qs<HTMLFormElement>('#mcq-form');
  if (!dialog || !form) return;

  const subjectSel = qs<HTMLSelectElement>('#mcq-subject');
  if (subjectSel && subjectSel.options.length <= 1) {
    subjectSel.innerHTML = '<option value="">— select subject —</option>' + subjects.map((s) => `<option value="${s.id}">${s.name}</option>`).join('');
  }

  qs<HTMLInputElement>('#mcq-id')!.value = m ? String(m.id) : '';
  qs<HTMLInputElement>('#mcq-subject')!.value = m ? String(m.subject_id) : '';
  qs<HTMLSelectElement>('#mcq-difficulty')!.value = m?.difficulty || 'M';
  qs<HTMLTextAreaElement>('#mcq-question')!.value = m?.question || '';
  qs<HTMLInputElement>('#mcq-opt-a')!.value = m?.opt_a || '';
  qs<HTMLInputElement>('#mcq-opt-b')!.value = m?.opt_b || '';
  qs<HTMLInputElement>('#mcq-opt-c')!.value = m?.opt_c || '';
  qs<HTMLInputElement>('#mcq-opt-d')!.value = m?.opt_d || '';
  qs<HTMLSelectElement>('#mcq-answer')!.value = (m?.answer || 'A').toUpperCase();
  qs<HTMLTextAreaElement>('#mcq-explanation')!.value = m?.explanation || '';
  qs<HTMLElement>('#mcq-form-title')!.textContent = m ? 'Edit MCQ' : 'Add MCQ';

  form.onsubmit = async (e) => {
    e.preventDefault();
    const id = qs<HTMLInputElement>('#mcq-id')!.value;
    const payload = {
      subject_id: Number(qs<HTMLSelectElement>('#mcq-subject')!.value),
      difficulty: qs<HTMLSelectElement>('#mcq-difficulty')!.value,
      question: qs<HTMLTextAreaElement>('#mcq-question')!.value,
      opt_a: qs<HTMLInputElement>('#mcq-opt-a')!.value,
      opt_b: qs<HTMLInputElement>('#mcq-opt-b')!.value,
      opt_c: qs<HTMLInputElement>('#mcq-opt-c')!.value,
      opt_d: qs<HTMLInputElement>('#mcq-opt-d')!.value,
      answer: qs<HTMLSelectElement>('#mcq-answer')!.value,
      explanation: qs<HTMLTextAreaElement>('#mcq-explanation')!.value,
    };
    if (!payload.subject_id || !payload.question) {
      toast('Subject and question required', 'error');
      return;
    }
    try {
      await api(id ? `/api/mcqs/${id}` : '/api/mcqs', { method: 'POST', body: JSON.stringify(payload) });
      dialog.close();
      toast('MCQ saved', 'success');
      qs<HTMLButtonElement>('#btn-reload-list')?.click();
    } catch (err) {
      toast(String(err), 'error');
    }
  };
  dialog.showModal();
  qs<HTMLButtonElement>('#mcq-cancel')!.onclick = () => dialog.close();

  const aiBtn = qs<HTMLButtonElement>('#btn-mcq-ai');
  aiBtn!.onclick = async () => {
    const sid = Number(qs<HTMLSelectElement>('#mcq-subject')!.value);
    if (!sid) {
      toast('Pick a subject first', 'error');
      return;
    }
    aiBtn!.disabled = true;
    aiBtn!.classList.add('sparkle');
    aiBtn!.textContent = 'Generating…';
    try {
      const phaseId = Number(qs<HTMLSelectElement>('#mcq-phase')?.value || 0);
      const r = await api<{ message: string; mcqs: any[] }>('/api/ai/generate', {
        method: 'POST',
        body: JSON.stringify({ subject_id: sid, count: 5, difficulty: 'auto', phase_id: phaseId || undefined }),
      });
      toast(r.message, 'success');
      dialog.close();
      qs<HTMLButtonElement>('#btn-reload-list')?.click();
    } catch (err) {
      toast(String(err), 'error');
    } finally {
      aiBtn!.disabled = false;
      aiBtn!.classList.remove('sparkle');
      aiBtn!.textContent = '🤖 AI generate (level-adaptive)';
    }
  };
}

function openBulkDialog(): void {
  const dialog = el('dialog', { class: 'modal modal-middle bg-slate-950/70 text-slate-100' });
  const inner = el('div', { class: 'modal-box max-w-2xl bg-slate-900 border border-slate-800' });
  inner.append(el('h3', { class: 'font-bold' }, ['Bulk paste MCQs']));
  inner.append(el('p', { class: 'mt-1 text-xs text-slate-500' }, ['One per line: `question | A | B | C | D | key | explanation`']));

  const subj = el('select', { class: 'select select-bordered mt-3 w-full bg-slate-800' });
  subj.innerHTML = '<option value="">— subject —</option>' + subjects.map((s) => `<option value="${s.id}">${s.name}</option>`).join('');
  inner.append(subj);

  const ta = el('textarea', { class: 'textarea textarea-bordered mt-3 w-full bg-slate-800', rows: '10', placeholder: 'Q1 | A1 | B1 | C1 | D1 | A | explanation...\nQ2 | ...' });
  inner.append(ta);

  const ops = el('div', { class: 'modal-action' });
  const cancel = el('button', { class: 'btn' }, ['Cancel']);
  const save = el('button', { class: 'btn btn-primary' }, ['Import']);
  cancel.onclick = () => dialog.remove();
  save.onclick = async () => {
    const sid = Number(subj.value);
    if (!sid) { toast('Choose a subject', 'error'); return; }
    let added = 0;
    const lines = ta.value.split('\n').map((l) => l.trim()).filter(Boolean);
    for (const line of lines) {
      const p = line.split('|').map((x) => x.trim());
      if (p.length < 6) continue;
      const payload = {
        subject_id: sid,
        question: p[0],
        opt_a: p[1], opt_b: p[2], opt_c: p[3], opt_d: p[4],
        answer: p[5].charAt(0).toUpperCase(),
        explanation: p[6] || '',
      };
      if (!['A', 'B', 'C', 'D'].includes(payload.answer)) continue;
      try {
        await api('/api/mcqs', { method: 'POST', body: JSON.stringify(payload) });
        added++;
      } catch { /* skip malformed */ }
    }
    dialog.remove();
    toast(`Imported ${added} MCQs`, 'success');
    qs<HTMLButtonElement>('#btn-reload-list')?.click();
  };
  ops.append(cancel, save);
  inner.append(ops);
  dialog.append(inner);
  dialog.addEventListener('click', (e) => {
    if (e.target === dialog) dialog.remove();
  });
  dialog.addEventListener('cancel', () => dialog.remove());
  document.body.append(dialog);
  dialog.showModal();
}
