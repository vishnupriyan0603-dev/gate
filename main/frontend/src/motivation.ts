import { toast, burst } from './ui';
import { reducedMotion } from './fx';

// Offline-first motivational engine: page-tagged quote bank,
// slow hero tickers on every page, calm milestone cheers.
// Nothing blocks study; everything respects reduced motion.

const QUOTES: Record<string, string[]> = {
  course: [
    'Roadmaps work when you walk them — one topic at a time.',
    'Finish the phase, don\'t perfect it.',
    'Done topics compound into syllabus coverage.',
  ],
  training: [
    'Wrong answers today prevent wrong answers in February.',
    'Speed is a skill — train it like one.',
    'Ten honest questions beat a hundred skimmed.',
  ],
  performance: [
    'Trust the trend, not the bad day.',
    'Weak areas are just future marks.',
    'Measure weekly, improve daily.',
  ],
  calendar: [
    'A planned day is a kept streak.',
    'Tick today, thank yourself in a month.',
    'Streaks are built on boring days.',
  ],
  documents: [
    'Read with a pen, remember with a drill.',
    'Notes you revisit beat notes you rewrite.',
    'One formula card a day keeps forgetting away.',
  ],
  learn: [
    'Understand first, memorize second.',
    'If you can\'t explain it simply, drill it again.',
    'Derivations stick longer than statements.',
  ],
  challenge: [
    'Ten minutes. Ten questions. Zero excuses.',
    'Pressure practice makes exam day feel normal.',
    'Play for the bonus, stay for the growth.',
  ],
  settings: [
    'Systems beat motivation — tune yours.',
    'Sync the plan, then trust the plan.',
    'Environment design is half the battle.',
  ],
  general: [
    'Small steps daily beat last-month panic.',
    'Consistency compounds — show up today.',
    'Every solved PYQ is interest on yesterday\'s effort.',
    'A rank under 100 is built one focused hour at a time.',
  ],
};

const PRAISE = [
  'Quest cleared. The streak salutes you. 🔥',
  'Banked. Future-you says thanks. 🎯',
  'Another brick in the AIR wall. 🧱',
  'Discipline 1, excuses 0. Keep going. 💪',
];

function dayIndex(): number {
  const now = new Date();
  const start = new Date(now.getFullYear(), 0, 0);
  return Math.floor((now.getTime() - start.getTime()) / 86400000);
}

export function quoteFor(page: string, n = 0): string {
  const bank = QUOTES[page] && QUOTES[page].length ? QUOTES[page] : QUOTES.general;
  return bank[(dayIndex() + n) % bank.length];
}

// Slow fade rotation for every [data-quote] hero line.
// Seeds with the server-rendered line so first paint is meaningful.
export function initTickers(page: string): void {
  const els = Array.from(document.querySelectorAll<HTMLElement>('[data-quote]'));
  if (els.length === 0) return;
  els.forEach((elm, ei) => {
    const seed = (elm.textContent || '').trim();
    const bank = QUOTES[page] && QUOTES[page].length ? QUOTES[page] : QUOTES.general;
    const lines = seed ? [seed, ...bank.filter((q) => q !== seed)] : [...bank];
    let li = (dayIndex() + ei) % lines.length;
    if (reducedMotion()) {
      elm.textContent = lines[li];
      return;
    }
    const swap = () => {
      if (document.hidden) return;
      li = (li + 1) % lines.length;
      elm.classList.remove('animate__animated', 'animate__fadeIn');
      elm.classList.add('animate__animated', 'animate__fadeOut');
      window.setTimeout(() => {
        elm.textContent = lines[li];
        elm.classList.remove('animate__fadeOut');
        elm.classList.add('animate__fadeIn');
        window.setTimeout(() => elm.classList.remove('animate__animated', 'animate__fadeIn'), 700);
      }, 450);
    };
    const timer = window.setInterval(swap, 9000);
    elm.addEventListener('pointerenter', () => window.clearInterval(timer), { once: true });
  });
}

// One gentle quote toast per day, dismissible, never a modal.
export function dailyToast(page: string): void {
  if (reducedMotion()) return;
  try {
    const today = new Date().toISOString().slice(0, 10);
    if (localStorage.getItem('gate_motd') === today) return;
    localStorage.setItem('gate_motd', today);
  } catch {
    return;
  }
  window.setTimeout(() => {
    toast(`💡 ${quoteFor(page)}`, 'info', 5200);
  }, 1600);
}

// Mission-clear celebration: XP toast now, praise line trailing behind.
export function missionCheer(message: string): void {
  toast(message, 'success');
  burst();
  if (reducedMotion()) return;
  window.setTimeout(() => {
    toast(PRAISE[Math.floor(Math.random() * PRAISE.length)], 'info', 3600);
  }, 1100);
}

export function initMotivation(page: string): void {
  initTickers(page);
  dailyToast(page);
}
