import { fxStart, fxDone } from './fx';

const base = (window as any).GATE_BASE || '';

export async function api<T = any>(path: string, options: RequestInit = {}): Promise<T> {
  fxStart();
  try {
    const res = await fetch(base + path, {
      headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
      ...options,
    });
    if (!res.ok) {
      let msg = `HTTP ${res.status}`;
      try {
        const j = await res.json();
        if (j.message) msg = j.message;
      } catch { /* ignore */ }
      throw new Error(msg);
    }
    const ct = res.headers.get('content-type') || '';
    if (ct.includes('application/json')) return res.json() as Promise<T>;
    return (await res.text()) as unknown as T;
  } finally {
    fxDone();
  }
}

export function qs<T extends HTMLElement>(sel: string, el: ParentNode = document): T | null {
  return el.querySelector<T>(sel);
}

export function qsa<T extends HTMLElement>(sel: string, el: ParentNode = document): T[] {
  return Array.from(el.querySelectorAll<T>(sel));
}

export function el<K extends keyof HTMLElementTagNameMap>(
  tag: K,
  attrs: Record<string, string> = {},
  children: (string | Node)[] = [],
): HTMLElementTagNameMap[K] {
  const node = document.createElement(tag);
  for (const [k, v] of Object.entries(attrs)) node.setAttribute(k, v);
  for (const c of children) node.append(c as any);
  return node;
}

export function fmt(minutes: number): string {
  if (minutes >= 60) return `${(minutes / 60).toFixed(1)}h`;
  return `${minutes}m`;
}

export function todayStr(): string {
  return todayStrIST();
}

// South-India standard: DISPLAY DD-MM-YYYY (IST), STORAGE/API stays YYYY-MM-DD.
export const IST_TIMEZONE = 'Asia/Kolkata';

export function todayStrIST(now: Date = new Date()): string {
  const parts = new Intl.DateTimeFormat('en-CA', {
    timeZone: IST_TIMEZONE,
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  }).format(now);
  return parts; // en-CA yields YYYY-MM-DD
}

export function fmtDMY(iso: string): string {
  const m = /^(\d{4})-(\d{2})-(\d{2})/.exec(iso);
  if (m) return `${m[3]}-${m[2]}-${m[1]}`;
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return iso;
  const parts = new Intl.DateTimeFormat('en-IN', {
    timeZone: IST_TIMEZONE,
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }).format(d);
  return parts.replaceAll('/', '-');
}