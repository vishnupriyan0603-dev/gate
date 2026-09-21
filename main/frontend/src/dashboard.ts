import dayjs from 'dayjs';
import { api, fmtDMY, qs, qsa, todayStr } from './api';
import { ring, toast, burst } from './ui';
import { missionCheer } from './motivation';
import { pop, withLoading } from './fx';
import { mountChart, emptyState, paintDone, perDayAccuracy, accuracyOption } from './charts';

export function init(): void {
  initRings();
  initBars();
  initMission();
  initTimer();
  initAccuracy();
  initHeatmap();
  initXpFill();
  initConstellation();
}

function initRings(): void {
  qsa<HTMLElement>('.progress-ring').forEach((n) => {
    const p = Number(n.dataset.pct || '0');
    n.innerHTML = ring(p, 64, 6, n.dataset.color || undefined);
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

function initMission(): void {
  const btn = qs<HTMLButtonElement>('#btn-done-mission');
  if (!btn) return;
  btn.addEventListener('click', async () => {
    pop(btn);
    const date = btn.dataset.date || todayStr();
    try {
      await api(`/api/calendar/${date}/status`, {
        method: 'POST',
        body: JSON.stringify({ status: 'done' }),
      });
      btn.textContent = 'Done ✓';
      btn.classList.add('btn-success');
      btn.classList.remove('btn-primary');
      missionCheer('Mission complete · +20 XP');
    } catch (e) {
      toast(String(e), 'error');
    }
  });
}

function initTimer(): void {
  const openBtn = qs<HTMLButtonElement>('#btn-timer');
  const startBtn = qs<HTMLButtonElement>('#timer-start');
  const stopBtn = qs<HTMLButtonElement>('#timer-stop');
  const display = qs<HTMLElement>('#timer-display') || qs<HTMLElement>('#timer-digits');
  const dialog = qs<HTMLDialogElement>('#study-timer-modal');
  if (!openBtn || !dialog || !startBtn || !stopBtn || !display) return;

  let seconds = 0;
  let interval: ReturnType<typeof setInterval> | null = null;
  const paint = () => {
    const label = `${String(Math.floor(seconds / 60)).padStart(2, '0')}:${String(seconds % 60).padStart(2, '0')}`;
    display.textContent = label;
    const alias = display.id === 'timer-display' ? qs<HTMLElement>('#timer-digits') : qs<HTMLElement>('#timer-display');
    if (alias) alias.textContent = label;
  };

  openBtn.addEventListener('click', () => dialog.showModal());
  qs<HTMLButtonElement>('#timer-close-btn')?.addEventListener('click', () => dialog.close());
  startBtn.addEventListener('click', () => {
    startBtn.disabled = true;
    stopBtn.disabled = false;
    pop(startBtn);
    interval = setInterval(() => {
      seconds++;
      paint();
    }, 1000);
  });
  stopBtn.addEventListener('click', async () => {
    if (interval) clearInterval(interval);
    interval = null;
    const minutes = Math.max(1, Math.round(seconds / 60));
    try {
      const r = await api<{ xp: number }>('/api/study/log', {
        method: 'POST',
        body: JSON.stringify({ minutes }),
      });
      toast(`Focus logged: ${minutes} min · +${r.xp} XP`, 'success');
      burst();
      dialog.close();
      const studiedDisplay = qs<HTMLElement>('#studied-today-display');
      if (studiedDisplay) {
        const currentText = studiedDisplay.textContent?.trim() || '00:00';
        const [h, m] = currentText.split(':').map(Number);
        const totalMin = (h || 0) * 60 + (m || 0) + minutes;
        studiedDisplay.textContent = `${String(Math.floor(totalMin / 60)).padStart(2, '0')}:${String(totalMin % 60).padStart(2, '0')}`;
      }
      const statMin = qs<HTMLElement>('#stat-minutes');
      if (statMin) statMin.textContent = String(Number(statMin.textContent || '0') + minutes);
    } catch (e) {
      toast(String(e), 'error');
    }
    seconds = 0;
    paint();
    startBtn.disabled = false;
    stopBtn.disabled = true;
  });
}

function initXpFill(): void {
  qsa<HTMLElement>('.xp-fill').forEach((f) => {
    const w = f.dataset.w || '0';
    requestAnimationFrame(() => requestAnimationFrame(() => {
      f.style.width = `${w}%`;
    }));
  });
}

function initConstellation(): void {
  const host = qs<HTMLElement>('#viz3d') || qs<HTMLElement>('#viz3d-canvas');
  if (!host) return;
  withLoading(
    host,
    import('./viz3d').then((m) => m.initConstellation(host)),
  ).catch((e) => {
    console.error('[GATE] constellation failed', e);
    host.innerHTML = '<p class="p-6 text-sm text-slate-500">3D view unavailable on this device.</p>';
  });
}

function initAccuracy(): void {
  const host = qs<HTMLElement>('#accuracy-chart');
  if (!host) return;
  host.classList.add('is-loading');
  api<{ recent: any[] }>('/api/overview')
    .then((d) => {
      const points = perDayAccuracy([...(d.recent ?? [])].reverse());
      if (points.length === 0) {
        emptyState(host, 'No practice yet — head to Training Arena to earn your first XP.', { href: 'training', label: 'Open Training Arena →' });
        return;
      }
      paintDone(host);
      mountChart(host, accuracyOption(points));
    })
    .catch(() => {
      emptyState(host, 'Could not load accuracy trend — reload to retry.');
    });
}

function initHeatmap(): void {
  const host = qs<HTMLElement>('#heatmap') || qs<HTMLElement>('#study-heatmap');
  if (!host) return;
  api<{ session_date: string; minutes: number }[]>('/api/study/heatmap')
    .then((rows) => {
      if (!rows || rows.length === 0) {
        host.innerHTML = '<div class="empty-state mx-auto max-w-xs px-4 py-6 text-center text-xs text-slate-400">No focus sessions yet — launch the Focus Timer above to start your streak.</div>';
        return;
      }
      const map = new Map<string, number>();
      for (const r of rows) map.set(r.session_date, Number(r.minutes));
      const start = dayjs().subtract(119, 'day');
      const cells: HTMLDivElement[] = [];
      for (let i = 0; i < 120; i++) {
        const date = start.add(i, 'day').format('YYYY-MM-DD');
        const min = map.get(date) || 0;
        const c = document.createElement('div');
        c.className = 'heat-cell h-3 w-3 rounded-[3px] anim-fade-up';
        c.style.setProperty('--d', `${(i % 30) * 22}ms`);
        c.title = `${fmtDMY(date)}: ${min} min`;
        c.style.background = min === 0 ? '#1a2340' : min < 30 ? '#4c1d95' : min < 90 ? '#7c3aed' : '#f59e0b';
        cells.push(c);
      }
      const grid = document.createElement('div');
      grid.className = 'grid grid-flow-col grid-rows-7 gap-1 overflow-x-auto py-1';
      grid.setAttribute('style', 'grid-auto-columns: max-content;');
      for (const cell of cells) {
        grid.append(cell);
      }
      host.append(grid);
    })
    .catch(() => { /* empty */ });
}