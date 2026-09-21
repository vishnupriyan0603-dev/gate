import { reducedMotion } from './fx';

// LottieFly: declarative micro-animations — <div data-lottie-fly="trophy"></div>.
// Files resolve to /lottie/<name>.json (free LottieFiles + custom loader/success-check).
// Letter/SVG fallback stays on error; honors reduced-motion + gate_fx=off.
const FLY_MAP: Record<string, string> = {
  loader: 'loader',
  success: 'success-check',
  'success-check': 'success-check',
  empty: 'empty-box',
  trophy: 'trophy',
  rocket: 'rocket',
  timer: 'timer-ring',
  book: 'book-flip',
  confetti: 'confetti-pop',
};

function fxOff(): boolean {
  try {
    return localStorage.getItem('gate_fx') === 'off';
  } catch {
    return false;
  }
}

export function initLottieFly(root: ParentNode = document): void {
  const hosts = Array.from(root.querySelectorAll<HTMLElement>('[data-lottie-fly]:not([data-fly-done])'));
  if (hosts.length === 0) return;
  const base = (window as { GATE_BASE?: string }).GATE_BASE || '';
  if (reducedMotion() || fxOff()) return;
  let observer: IntersectionObserver | null = null;
  const playOnView = (host: HTMLElement, anim: { play?: () => void; pause?: () => void }) => {
    if (typeof IntersectionObserver === 'undefined') {
      anim.play?.();
      return;
    }
    if (!observer) {
      observer = new IntersectionObserver(
        (entries) => {
          for (const en of entries) {
            const a = (en.target as HTMLElement & { __fly?: { play?: () => void; pause?: () => void } }).__fly;
            if (!a) continue;
            if (en.isIntersecting) a.play?.();
            else a.pause?.();
          }
        },
        { threshold: 0.15 },
      );
    }
    (host as HTMLElement & { __fly?: unknown }).__fly = anim;
    observer.observe(host);
  };
  void import('lottie-web')
    .then(({ default: lottie }) => {
      for (const host of hosts) {
        if (host.dataset.flyDone) continue;
        host.dataset.flyDone = '1';
        const key = (host.dataset.lottieFly || 'success').toLowerCase();
        const file = FLY_MAP[key] || 'star';
        try {
          const anim = lottie.loadAnimation({
            container: host,
            renderer: 'svg',
            loop: key === 'loader',
            autoplay: false,
            path: `${base}/lottie/${file}.json`,
          });
          playOnView(host, anim);
        } catch {
          /* fallback content stays */
        }
      }
    })
    .catch(() => {});
}
