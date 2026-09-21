import confetti from 'canvas-confetti';
import { el } from './api';

const TOAST_META: Record<string, [string, string]> = {
  success: ['bg-emerald-500/95 border-emerald-400/40', '✓'],
  error: ['bg-rose-500/95 border-rose-400/40', '✕'],
  info: ['bg-violet-600/95 border-violet-400/40', 'ℹ'],
};

function toastStack(): HTMLElement {
  let stack = document.getElementById('toast-stack');
  if (!stack) {
    stack = el('div', { id: 'toast-stack', 'aria-live': 'polite' });
    document.body.append(stack);
  }
  return stack;
}

export function toast(message: string, type: 'success' | 'error' | 'info' = 'info', ms = 3000) {
  const stack = toastStack();
  while (stack.children.length >= 4) stack.firstElementChild?.remove();
  const meta = TOAST_META[type] || TOAST_META.info;
  const box = el('div', {
    class: `g-toast toast-${type} animate__animated animate__fadeInRight ${meta[0]} text-white px-4 py-3 pr-6 rounded-xl shadow-2xl text-sm font-medium border backdrop-blur`,
    role: 'status',
    'data-type': type,
  });
  box.append(el('span', { class: 'mr-2 font-bold' }, [meta[1]]));
  box.append(el('span', {}, [message]));
  const bar = el('span', { class: 'bar' });
  bar.style.animationDuration = `${ms}ms`;
  box.append(bar);
  stack.append(box);
  const kill = () => {
    box.classList.remove('animate__fadeInRight');
    box.classList.add('animate__fadeOutRight');
    window.setTimeout(() => box.remove(), 320);
  };
  const timer = window.setTimeout(kill, ms);
  box.addEventListener('click', () => {
    window.clearTimeout(timer);
    kill();
  });
}

export function burst() {
  confetti({ particleCount: 90, spread: 70, origin: { y: 0.7 } });
}

export function fireworks() {
  const end = Date.now() + 1400;
  const frame = () => {
    confetti({ particleCount: 4, angle: 60, spread: 60, origin: { x: 0, y: 0.75 } });
    confetti({ particleCount: 4, angle: 120, spread: 60, origin: { x: 1, y: 0.75 } });
    if (Date.now() < end) requestAnimationFrame(frame);
  };
  frame();
  confetti({ particleCount: 160, spread: 100, origin: { y: 0.6 }, colors: ['#818cf8', '#22d3ee', '#f0abfc', '#fbbf24'] });
}

export function celebrate(accuracy: number): void {
  if (accuracy >= 100) fireworks();
  else if (accuracy >= 80) burst();
}

export async function initIcons(icons: Record<string, any> = {}): Promise<void> {
  const { createIcons } = await import('lucide');
  createIcons({ icons, attrs: { width: '18', height: '18' } });
}

export function ring(percent: number, size = 64, stroke = 6, color?: string): string {
  const r = (size - stroke) / 2;
  const c = 2 * Math.PI * r;
  const off = c * (1 - Math.min(100, Math.max(0, percent)) / 100);
  const arc = color
    ? `stroke="${color}"`
    : `stroke="url(#questGrad${size})"`;
  const defs = color
    ? ''
    : `<defs><linearGradient id="questGrad${size}" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#7c3aed"/><stop offset="100%" stop-color="#22d3ee"/>
    </linearGradient></defs>`;
  const glow = color ? ` style="filter: drop-shadow(0 0 5px ${color}66)"` : '';
  return `<svg class="progress-ring" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}"${glow}>
    <circle cx="${size/2}" cy="${size/2}" r="${r}" fill="none" stroke="#263154" stroke-width="${stroke}"/>
    <circle cx="${size/2}" cy="${size/2}" r="${r}" fill="none" ${arc} stroke-width="${stroke}"
      stroke-linecap="round" stroke-dasharray="${c}" stroke-dashoffset="${off}" transform="rotate(-90 ${size/2} ${size/2})"/>
    ${defs}
    <text x="50%" y="50%" dy=".32em" text-anchor="middle" font-size="${size*0.22}" fill="currentColor">${Math.round(percent)}%</text>
  </svg>`;
}

export function masteryLevel(pct: number): { name: string; color: string; cls: string } {
  if (pct >= 95) return { name: 'Legend', color: '#f59e0b', cls: 'lvl-gold' };
  if (pct >= 80) return { name: 'Master', color: '#10b981', cls: 'lvl-green' };
  if (pct >= 60) return { name: 'Scholar', color: '#e2e8f0', cls: 'lvl-white' };
  if (pct >= 40) return { name: 'Rising', color: '#38bdf8', cls: 'lvl-blue' };
  if (pct >= 20) return { name: 'Rookie', color: '#fb4d6d', cls: 'lvl-red' };
  return { name: 'Seed', color: '#64748b', cls: 'lvl-black' };
}

export function xpToLevel(xp: number): { level: number; current: number; need: number; pct: number } {
  const level = Math.floor(xp / 500) + 1;
  const current = xp % 500;
  return { level, current, need: 500, pct: Math.round((current / 500) * 100) };
}