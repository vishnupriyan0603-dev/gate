import * as echarts from 'echarts';
import dayjs from 'dayjs';
import { api, fmtDMY, qs, el } from './api';
import { ring, masteryLevel } from './ui';
import {
  baseOpts,
  mountChart,
  emptyState,
  paintDone,
  perDayAccuracy,
  perDayCounts,
  accuracyOption,
} from './charts';

export function init(): void {
  if (qs<HTMLElement>('#chart-accuracy')) {
    loadOverview();
    loadHeatmap();
    loadTopics();
  }
}

async function loadOverview(): Promise<void> {
  const hosts = ['#chart-accuracy', '#chart-subjects', '#chart-attempts']
    .map((s) => qs<HTMLElement>(s))
    .filter((h): h is HTMLElement => !!h);
  hosts.forEach((h) => h.classList.add('is-loading'));
  let d: { recent: any[]; xp: number; streak: number; subjects: any[] };
  try {
    d = await api<{ recent: any[]; xp: number; streak: number; subjects: any[] }>('/api/overview');
  } catch {
    for (const h of hosts) emptyState(h, 'Could not load analytics — check your connection and reload.');
    return;
  }
  const recent = [...d.recent].reverse();

  // Accuracy over time (aggregated per day, sorted, target line)
  const accHost = qs<HTMLElement>('#chart-accuracy');
  if (accHost) {
    const points = perDayAccuracy(recent);
    if (points.length > 0) {
      paintDone(accHost);
      mountChart(accHost, accuracyOption(points));
    } else {
      emptyState(accHost, 'No practice history yet — take a quiz in Training mode.', { href: 'training', label: 'Open Training Arena →' });
    }
  }

  // Subject-wise accuracy
  const subHost = qs<HTMLElement>('#chart-subjects');
  if (subHost) {
    paintDone(subHost);
    const agg: Record<number, { name: string; correct: number; total: number }> = {};
    for (const r of recent) {
      const id = r.subject_id ?? 0;
      if (!agg[id]) agg[id] = { name: r.subject_name ?? `Subject ${id}`, correct: 0, total: 0 };
      agg[id].correct += Number(r.correct);
      agg[id].total += Number(r.total);
    }
    const items = Object.values(agg)
      .filter((s) => s.total > 0)
      .map((s) => ({ name: s.name, value: Math.round((s.correct / s.total) * 100) }))
      .sort((a, b) => a.value - b.value);
    if (items.length > 0) {
      paintDone(subHost);
      mountChart(subHost, baseOpts({
        grid: { left: 8, right: 16, top: 16, bottom: 8, containLabel: true },
        xAxis: { type: 'value', min: 0, max: 100, axisLabel: { color: '#64748b', fontSize: 9, formatter: '{value}%' }, splitLine: { lineStyle: { color: '#263154' } } },
        yAxis: { type: 'category', data: items.map((i) => i.name), axisLabel: { color: '#94a3b8', fontSize: 10 }, axisTick: { show: false } },
        series: [{ type: 'bar', data: items.map((i) => ({ value: i.value, itemStyle: { color: i.value >= 70 ? '#10b981' : i.value >= 50 ? '#f59e0b' : '#fb4d6d', borderRadius: [4, 8, 8, 4] } })), barWidth: 18, label: { show: true, position: 'right', color: '#94a3b8', fontSize: 10, formatter: '{c}%' } }],
      }));
    } else {
      emptyState(subHost, 'Subject-wise accuracy appears after practicing.');
    }
  }

  // Attempts over time (per ISO day, sorted, gaps filled)
  const attHost = qs<HTMLElement>('#chart-attempts');
  if (attHost) {
    const points = perDayCounts(recent);
    if (points.length > 0) {
      paintDone(attHost);
      mountChart(attHost, baseOpts({
        tooltip: {
          trigger: 'axis',
          backgroundColor: '#131a32',
          borderColor: '#334155',
          textStyle: { color: '#e2e8f0', fontSize: 11 },
          formatter: (p: any) => { const i = p?.[0]?.dataIndex ?? 0; const pt = points[i]; return pt ? `${pt.full}: ${pt.total} quiz${pt.total === 1 ? '' : 'zes'}` : ''; },
        },
        xAxis: { type: 'category', data: points.map((p) => p.label), axisLabel: { color: '#64748b', fontSize: 9, hideOverlap: true }, axisLine: { lineStyle: { color: '#334155' } }, axisTick: { show: false } },
        yAxis: { type: 'value', minInterval: 1, axisLabel: { color: '#64748b', fontSize: 9 }, splitLine: { lineStyle: { color: '#263154' } } },
        series: [{ type: 'bar', data: points.map((p) => p.total), barWidth: 16, itemStyle: { color: '#7c3aed', borderRadius: [4, 4, 0, 0] } }],
      }));
    } else {
      emptyState(attHost, 'Attempts over time will appear here.');
    }
  }

  // Weak areas
  const weakHost = qs<HTMLElement>('#weak-areas');
  if (weakHost) {
    const agg: Record<number, { name: string; correct: number; total: number }> = {};
    for (const r of recent) {
      const id = r.subject_id ?? 0;
      if (!id) continue;
      if (!agg[id]) agg[id] = { name: r.subject_name ?? `Subject ${id}`, correct: 0, total: 0 };
      agg[id].correct += Number(r.correct);
      agg[id].total += Number(r.total);
    }
    const list = Object.values(agg)
      .map((s) => ({ ...s, pct: s.total ? Math.round((s.correct / s.total) * 100) : 0 }))
      .sort((a, b) => a.pct - b.pct)
      .slice(0, 6);
    weakHost.innerHTML = '';
    for (const [i, s] of list.entries()) {
      const card = el('div', { class: 'anim-fade-up study-card rounded-xl p-4' });
      card.style.setProperty('--d', `${Math.min(i, 5) * 60}ms`);
      const top = el('div', { class: 'flex items-center justify-between gap-2' });
      top.append(
        el('p', { class: 'text-sm font-bold text-white truncate' }, [s.name]),
        el('span', { class: 'chip chip-streak text-[10px] font-mono' }, [`${s.pct}% Acc`]),
      );
      card.append(top);
      card.append(el('p', { class: 'text-xs text-slate-400 mt-1.5' }, [`${s.total} attempts logged in bank`]));
      const link = el('a', {
        href: `/training`,
        class: 'btn btn-xs btn-gold w-full mt-3 justify-center gap-1.5 rounded-xl',
      }, ['Drill this subject →']);
      card.append(link);
      weakHost.append(card);
    }
    if (list.length === 0) weakHost.append(el('p', { class: 'text-sm text-slate-400 py-6 text-center col-span-full' }, ['No weak areas detected yet — keep taking practice quizzes!']));
  }
}

async function loadHeatmap(): Promise<void> {
  const host = qs<HTMLElement>('#chart-hours');
  if (!host) return;
  host.classList.add('is-loading');
  let rows: { session_date: string; minutes: number }[];
  try {
    rows = await api<{ session_date: string; minutes: number }[]>('/api/study/heatmap');
  } catch {
    emptyState(host, 'Could not load study minutes — reload to retry.');
    return;
  }
  const map = new Map<string, number>();
  for (const r of rows) map.set(String(r.session_date).slice(0, 10), Number(r.minutes) || 0);
  // Last 30 days with real date labels (was: 90 day-of-month numbers)
  const points: { iso: string; label: string; full: string; mins: number }[] = [];
  for (let i = 29; i >= 0; i--) {
    const iso = dayjs().subtract(i, 'day').format('YYYY-MM-DD');
    points.push({ iso, label: dayjs(iso).format('DD-MM'), full: fmtDMY(iso), mins: map.get(iso) || 0 });
  }
  paintDone(host);
  mountChart(host, baseOpts({
    tooltip: {
      trigger: 'axis',
      backgroundColor: '#131a32',
      borderColor: '#334155',
      textStyle: { color: '#e2e8f0', fontSize: 11 },
      formatter: (p: any) => { const i = p?.[0]?.dataIndex ?? 0; const pt = points[i]; return pt ? `${pt.full}: ${pt.mins} min` : ''; },
    },
    xAxis: { type: 'category', data: points.map((p) => p.label), axisLabel: { color: '#64748b', fontSize: 8, hideOverlap: true }, axisLine: { lineStyle: { color: '#334155' } }, axisTick: { show: false } },
    yAxis: { type: 'value', axisLabel: { color: '#64748b', fontSize: 8 }, splitLine: { lineStyle: { color: '#263154' } } },
    series: [{
      type: 'bar',
      data: points.map((p) => ({
        value: p.mins,
        itemStyle: { color: p.mins === 0 ? '#263154' : p.mins < 30 ? '#4c1d95' : p.mins < 90 ? '#10b981' : '#f59e0b', borderRadius: [3, 3, 0, 0] },
      })),
      barWidth: '60%',
    }],
  }));
}

async function loadTopics(): Promise<void> {
  const host = qs<HTMLElement>('#mastery');
  const tgtHost = qs<HTMLElement>('#chart-targets');
  if (!host && !tgtHost) return;
  tgtHost?.classList.add('is-loading');

  const [topics, targets] = await Promise.all([
    api<any[]>('/api/topics').catch(() => []),
    api<any[]>('/api/targets').catch(() => []),
  ]);

  if (host) {
    const agg: Record<number, { name: string; total: number; done: number }> = {};
    for (const t of topics) {
      const sid = t.subject_id ?? 0;
      if (!sid) continue;
      if (!agg[sid]) agg[sid] = { name: t.subject_name ?? `Sub ${sid}`, total: 0, done: 0 };
      agg[sid].total++;
      if (t.status === 'done') agg[sid].done++;
    }
    host.innerHTML = '';
    const list = Object.values(agg).sort((a, b) => (a.done / (a.total || 1)) - (b.done / (b.total || 1)));
    for (const [i, s] of list.entries()) {
      const pct = s.total ? Math.round((s.done / s.total) * 100) : 0;
      const lvl = masteryLevel(pct);
      const base = (window as { GATE_BASE?: string }).GATE_BASE || '';
      const row = el('div', { class: 'mastery-row anim-fade-up flex items-center gap-3' });
      row.style.setProperty('--d', `${Math.min(i, 7) * 50}ms`);
      row.style.setProperty('--lvl', lvl.color);
      row.setAttribute('data-click-fx', '');
      const mascot = el('span', { class: 'lottie-dot hidden h-8 w-8 shrink-0 overflow-hidden rounded-lg border border-white/10 bg-black/30 sm:grid sm:place-items-center' });
      mascot.dataset.lottie = s.name;
      const ringDiv = el('div', { class: 'progress-ring', innerHTML: ring(pct, 40, 5, lvl.color) });
      const info = el('div', { class: 'flex-1' });
      info.append(el('p', { class: 'text-sm text-slate-200' }, [s.name]));
      const track = el('div', { class: 'bar-track mt-1 h-1.5 w-full' });
      const fill = el('div', { class: 'bar-fill h-full rounded-full' });
      fill.dataset.w = String(pct);
      fill.style.background = lvl.color;
      requestAnimationFrame(() => requestAnimationFrame(() => {
        fill.style.width = `${pct}%`;
      }));
      track.append(fill);
      info.append(track);
      info.append(el('p', { class: 'mt-1 text-xs text-slate-500' }, [`${s.done}/${s.total} topics`]));
      row.append(mascot, ringDiv, info);
      row.append(el('span', { class: `chip ${lvl.cls} text-[10px] font-mono font-bold` }, [`● ${lvl.name} ${pct}%`]));
      row.append(el('a', { class: 'btn btn-xs btn-quest drill-btn rounded-lg whitespace-nowrap', href: `${base}/training` }, ['⚔ Drill']));
      host.append(row);
    }
  }

  if (tgtHost && targets.length > 0) {
    paintDone(tgtHost);
    const minVals = targets.map((t: any) => Number(t.target_min) || 0);
    const maxVals = targets.map((t: any) => Number(t.target_max) || 0);
    mountChart(tgtHost, baseOpts({
      tooltip: { trigger: 'axis', backgroundColor: '#131a32', borderColor: '#334155', textStyle: { color: '#e2e8f0', fontSize: 11 } },
      xAxis: { type: 'category', data: targets.map((t: any) => String(t.period)), axisLabel: { color: '#64748b', fontSize: 9, interval: 0, rotate: targets.length > 4 ? 24 : 0 }, axisLine: { lineStyle: { color: '#334155' } }, axisTick: { show: false } },
      yAxis: { type: 'value', min: 0, max: 100, interval: 25, axisLabel: { color: '#64748b', fontSize: 9, formatter: '{value}' }, splitLine: { lineStyle: { color: '#263154' } } },
      series: [
        { type: 'bar', data: minVals, barWidth: 14, itemStyle: { color: '#7c3aed', borderRadius: [4, 4, 0, 0] }, label: { show: true, position: 'top', color: '#94a3b8', fontSize: 9 }, name: 'Min target' },
        { type: 'line', data: maxVals, symbol: 'diamond', symbolSize: 8, lineStyle: { color: '#f59e0b', type: 'dashed', width: 2 }, itemStyle: { color: '#f59e0b' }, name: 'Max target' },
      ],
      legend: { bottom: 0, textStyle: { color: '#64748b', fontSize: 10 } },
    }));
  } else if (tgtHost) {
    paintDone(tgtHost);
    tgtHost.innerHTML = '<p class="py-16 text-center text-sm text-slate-500">Target trajectory will appear once targets are configured.</p>';
  }
}
