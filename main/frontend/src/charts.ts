import * as echarts from 'echarts';
import dayjs from 'dayjs';
import { fmtDMY } from './api';

// Shared ECharts helpers for dashboard + performance pages.
// Fixes: stale instances, zero-size containers (collapsed panels),
// cut-off axis labels, duplicate DD-MM keys, unsorted points,
// spinners stuck on API failure.

export interface DayPoint {
  iso: string;
  label: string;
  full: string;
  correct: number;
  total: number;
  accuracy: number;
}

export function baseOpts(overrides: Record<string, unknown> = {}): Record<string, unknown> {
  return {
    backgroundColor: 'transparent',
    animationDuration: 700,
    animationEasing: 'cubicOut',
    animationDelay: (idx: number) => Math.min(idx, 30) * 20,
    textStyle: { color: '#94a3b8' },
    grid: { left: 8, right: 12, top: 16, bottom: 8, containLabel: true },
    tooltip: {
      trigger: 'axis',
      backgroundColor: '#131a32',
      borderColor: '#334155',
      textStyle: { color: '#e2e8f0', fontSize: 11 },
    },
    ...overrides,
  };
}

export function paintDone(host: HTMLElement | null): void {
  host?.classList.remove('is-loading', 'chart-loading');
}

export function emptyState(host: HTMLElement | null, msg: string, cta?: { href: string; label: string }): void {
  if (!host) return;
  paintDone(host);
  host.innerHTML = '';
  const wrap = document.createElement('div');
  wrap.className = 'empty-state py-10 text-center';
  const p = document.createElement('p');
  p.className = 'text-sm text-slate-500';
  p.textContent = msg;
  wrap.append(p);
  if (cta) {
    const a = document.createElement('a');
    a.href = cta.href;
    a.className = 'btn btn-xs btn-gold mt-4 rounded-xl';
    a.textContent = cta.label;
    wrap.append(a);
  }
  host.append(wrap);
}

/** Mount (or remount) a chart: disposes stale instance, auto-resizes. */
export function mountChart(host: HTMLElement, option: Record<string, unknown>): echarts.ECharts {
  const prev = echarts.getInstanceByDom(host);
  if (prev) prev.dispose();
  const chart = echarts.init(host);
  chart.setOption(option);
  const resize = (): void => {
    if (!chart.isDisposed() && host.clientWidth > 0 && host.clientHeight > 0) chart.resize();
  };
  window.addEventListener('resize', resize);
  // Panels toggle .collapsed (display:none) — resize after expand transition.
  if (typeof ResizeObserver !== 'undefined') {
    const ro = new ResizeObserver(() => resize());
    ro.observe(host);
  }
  document.addEventListener('click', (e) => {
    const btn = (e.target as HTMLElement | null)?.closest?.('[data-panel-collapse]');
    if (btn) window.setTimeout(resize, 380);
  });
  return chart;
}

function isoOf(v: unknown): string {
  return String(v ?? '').slice(0, 10);
}

/** Aggregate quiz sessions per ISO day (weighted), sorted ascending. */
export function perDayAccuracy(recent: Array<Record<string, unknown>>): DayPoint[] {
  const map = new Map<string, { correct: number; total: number }>();
  for (const r of recent) {
    const iso = isoOf(r.created_at);
    if (!/^\d{4}-\d{2}-\d{2}$/.test(iso)) continue;
    const slot = map.get(iso) ?? { correct: 0, total: 0 };
    slot.correct += Number(r.correct ?? 0);
    slot.total += Number(r.total ?? 0);
    map.set(iso, slot);
  }
  return [...map.entries()]
    .sort(([a], [b]) => (a < b ? -1 : 1))
    .slice(-30)
    .map(([iso, s]) => ({
      iso,
      label: dayjs(iso).format('DD-MM'),
      full: fmtDMY(iso),
      correct: s.correct,
      total: s.total,
      accuracy: s.total > 0 ? Math.round((s.correct / s.total) * 100) : 0,
    }));
}

/** Count sessions per ISO day, sorted ascending, gaps filled with 0. */
export function perDayCounts(recent: Array<Record<string, unknown>>): DayPoint[] {
  const map = new Map<string, number>();
  for (const r of recent) {
    const iso = isoOf(r.created_at);
    if (!/^\d{4}-\d{2}-\d{2}$/.test(iso)) continue;
    map.set(iso, (map.get(iso) ?? 0) + 1);
  }
  const keys = [...map.keys()].sort();
  if (keys.length === 0) return [];
  const out: DayPoint[] = [];
  let cur = dayjs(keys[0]);
  const end = dayjs(keys[keys.length - 1]);
  while (!cur.isAfter(end, 'day')) {
    const iso = cur.format('YYYY-MM-DD');
    out.push({
      iso,
      label: cur.format('DD-MM'),
      full: fmtDMY(iso),
      correct: 0,
      total: map.get(iso) ?? 0,
      accuracy: 0,
    });
    cur = cur.add(1, 'day');
  }
  return out.slice(-30);
}

export function accuracyOption(points: DayPoint[]): Record<string, unknown> {
  const avg = points.length
    ? Math.round(points.reduce((s, p) => s + p.accuracy, 0) / points.length)
    : 0;
  return baseOpts({
    tooltip: {
      trigger: 'axis',
      backgroundColor: '#131a32',
      borderColor: '#334155',
      textStyle: { color: '#e2e8f0', fontSize: 11 },
      formatter: (p: Array<{ dataIndex: number; value: unknown }>) => {
        const i = p?.[0]?.dataIndex ?? 0;
        const pt = points[i];
        return pt ? `${pt.full}: ${pt.accuracy}% (${pt.correct}/${pt.total})` : `${p?.[0]?.value ?? ''}%`;
      },
    },
    xAxis: {
      type: 'category',
      boundaryGap: false,
      data: points.map((p) => p.label),
      axisLabel: { color: '#64748b', fontSize: 9, hideOverlap: true },
      axisLine: { lineStyle: { color: '#334155' } },
      axisTick: { show: false },
    },
    yAxis: {
      type: 'value',
      min: 0,
      max: 100,
      interval: 25,
      axisLabel: { color: '#64748b', fontSize: 9, formatter: '{value}%' },
      splitLine: { lineStyle: { color: '#263154' } },
    },
    series: [
      {
        name: 'Accuracy',
        type: 'line',
        data: points.map((p) => p.accuracy),
        smooth: true,
        symbol: 'circle',
        symbolSize: 6,
        showSymbol: points.length <= 15,
        lineStyle: { color: '#8b5cf6', width: 2.5 },
        itemStyle: { color: '#8b5cf6', borderColor: '#f59e0b', borderWidth: 1.5 },
        areaStyle: {
          color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
            { offset: 0, color: 'rgba(124,58,237,0.32)' },
            { offset: 1, color: 'rgba(124,58,237,0.04)' },
          ]),
        },
        markLine: {
          silent: true,
          symbol: 'none',
          label: { color: '#f59e0b', fontSize: 9, formatter: 'Target 75%' },
          lineStyle: { color: '#f59e0b', type: 'dashed', width: 1.5 },
          data: [{ yAxis: 75 }],
        },
        markPoint: points.length
          ? {
              symbol: 'pin',
              symbolSize: 36,
              itemStyle: { color: '#10b981' },
              label: { color: '#fff', fontSize: 9, formatter: `${avg}%` },
              data: [{ coord: [points[points.length - 1].label, points[points.length - 1].accuracy] }],
            }
          : undefined,
      },
    ],
  });
}
