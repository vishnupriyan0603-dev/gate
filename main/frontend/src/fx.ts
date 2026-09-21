import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

// Global animation / interaction utilities. Deliberately dependency-free
// apart from nprogress (never import api.ts here — api.ts imports this file
// for request tracking, so that would be a cycle).

export function reducedMotion(): boolean {
  return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

// ---------------- NProgress request tracking ----------------
NProgress.configure({ showSpinner: false, trickleSpeed: 140, minimum: 0.08 });
let inflight = 0;

export function fxStart(): void {
  if (reducedMotion()) return;
  if (++inflight === 1) NProgress.start();
}

export function fxDone(): void {
  if (reducedMotion()) {
    inflight = 0;
    return;
  }
  if (--inflight <= 0) {
    inflight = 0;
    NProgress.done(true);
  }
}

// ---------------- Scroll reveal ----------------
let revealObserver: IntersectionObserver | null = null;

export function initReveal(root: ParentNode = document): void {
  const els = Array.from(root.querySelectorAll<HTMLElement>('[data-reveal]:not(.revealed)'));
  if (els.length === 0) return;
  if (reducedMotion() || typeof IntersectionObserver === 'undefined') {
    els.forEach((e) => e.classList.add('revealed'));
    return;
  }
  if (!revealObserver) {
    revealObserver = new IntersectionObserver(
      (entries) => {
        for (const en of entries) {
          if (en.isIntersecting) {
            en.target.classList.add('revealed');
            revealObserver?.unobserve(en.target);
          }
        }
      },
      { threshold: 0.08, rootMargin: '0px 0px -6% 0px' },
    );
  }
  els.forEach((e) => revealObserver?.observe(e));
}

// ---------------- Animated counters ----------------
export function countUp(elm: HTMLElement, target: number, dur = 1200): void {
  if (reducedMotion() || !Number.isFinite(target)) {
    elm.textContent = String(Math.round(target));
    return;
  }
  const t0 = performance.now();
  const step = (t: number) => {
    const p = Math.min(1, (t - t0) / dur);
    const eased = 1 - Math.pow(1 - p, 3);
    elm.textContent = String(Math.round(target * eased));
    if (p < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
}

export function initCountUps(root: ParentNode = document): void {
  Array.from(root.querySelectorAll<HTMLElement>('[data-count]')).forEach((elm) => {
    if (elm.dataset.counted) return;
    elm.dataset.counted = '1';
    const target = Number(elm.dataset.count ?? elm.textContent ?? '0');
    if (!Number.isFinite(target)) return;
    const run = () => countUp(elm, target);
    if (reducedMotion()) {
      run();
      return;
    }
    const io = new IntersectionObserver(
      (entries) => {
        if (entries.some((e) => e.isIntersecting)) {
          io.disconnect();
          run();
        }
      },
      { threshold: 0.4 },
    );
    io.observe(elm);
  });
}

// ---------------- Micro-animators ----------------
function replay(elm: HTMLElement, cls: string, ms: number): void {
  elm.classList.remove(cls);
  void elm.offsetWidth; // reflow so the animation restarts
  elm.classList.add(cls);
  window.setTimeout(() => elm.classList.remove(cls), ms + 30);
}

export function shake(elm: HTMLElement): void {
  replay(elm, 'anim-shake', 450);
}

export function pop(elm: HTMLElement): void {
  replay(elm, 'anim-pop', 300);
}

export function flash(elm: HTMLElement, ok: boolean): void {
  replay(elm, ok ? 'anim-flash-ok' : 'anim-flash-err', 800);
}

// ---------------- Loading overlay helper ----------------
export function withLoading<T>(host: HTMLElement, work: Promise<T>): Promise<T> {
  host.classList.add('is-loading');
  const done = () => host.classList.remove('is-loading');
  work.then(done, done);
  return work;
}

// ---------------- Tooltips (tippy, lazy-loaded) ----------------
let tippyPromise: Promise<unknown> | null = null;

export function bindTips(root: ParentNode = document): Promise<void> {
  const els = Array.from(root.querySelectorAll<HTMLElement>('[data-tip]:not([data-tip-bound])'));
  if (els.length === 0) return Promise.resolve();
  els.forEach((e) => e.setAttribute('data-tip-bound', '1'));
  if (!tippyPromise) {
    tippyPromise = Promise.all([import('tippy.js'), import('tippy.js/dist/tippy.css')]).then(
      ([m]) => (m as { default: unknown }).default,
    );
  }
  return tippyPromise
    .then((tippy: unknown) => {
      (tippy as (t: HTMLElement[], o: Record<string, unknown>) => void)(els, {
        content: (ref: Element) => ref.getAttribute('data-tip') || '',
        theme: 'gate',
        delay: [250, 0],
        maxWidth: 280,
      });
    })
    .catch(() => undefined);
}

// ---------------- Keyboard helpers ----------------
export function isTyping(): boolean {
  const a = document.activeElement as HTMLElement | null;
  return (
    !!a &&
    (a.tagName === 'INPUT' || a.tagName === 'TEXTAREA' || a.tagName === 'SELECT' || a.isContentEditable)
  );
}

export function initShortcuts(): void {
  document.getElementById('top-search')?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      const q = (e.target as HTMLInputElement).value.trim();
      const base = (window as { GATE_BASE?: string }).GATE_BASE || '';
      location.href = q ? `${base}/documents?q=${encodeURIComponent(q)}` : `${base}/documents`;
    }
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === '/' && !isTyping()) {
      e.preventDefault();
      const s = document.querySelector<HTMLInputElement>('[data-search-focus]');
      if (s) {
        s.focus();
      } else {
        const base = (window as { GATE_BASE?: string }).GATE_BASE || '';
        location.href = `${base}/documents`;
      }
    }
  });
}

export function initMobileNav(): void {
  const open = document.getElementById('side-open');
  const scrim = document.getElementById('side-scrim');
  if (open) open.addEventListener('click', () => document.body.classList.add('side-open'));
  if (scrim) scrim.addEventListener('click', () => document.body.classList.remove('side-open'));
  // Legacy drawer (kept for any old markup still in DOM)
  const toggle = document.getElementById('mobile-nav-toggle');
  const drawer = document.getElementById('mobile-nav');
  if (!toggle || !drawer) return;
  toggle.addEventListener('click', () => {
    const hidden = drawer.classList.toggle('hidden');
    toggle.setAttribute('aria-expanded', hidden ? 'false' : 'true');
    if (!hidden) {
      drawer.classList.remove('animate__animated', 'animate__fadeInUp');
      void drawer.offsetWidth;
      drawer.classList.add('animate__animated', 'animate__fadeInUp');
    }
  });
}

// ---------------- View-transition page swaps ----------------
export function initViewTransitions(): void {
  const doc = document as Document & { startViewTransition?: (cb: () => void) => void };
  if (typeof doc.startViewTransition !== 'function' || reducedMotion()) return;
  document.addEventListener('click', (e) => {
    const anchor = (e.target as HTMLElement).closest?.('a[href]') as HTMLAnchorElement | null;
    if (!anchor || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
      return;
    }
    if (anchor.target && anchor.target !== '_self') return;
    if (anchor.hasAttribute('download')) return;
    const url = new URL(anchor.href, location.href);
    if (url.origin !== location.origin) return;
    if (url.pathname === location.pathname && url.search === location.search) return; // same-page anchor
    e.preventDefault();
    try {
      doc.startViewTransition?.(() => {
        location.href = anchor.href;
      });
    } catch {
      location.href = anchor.href;
    }
  });
}

// ---------------- One-shot bootstrap ----------------
export function initFx(): void {
  initReveal();
  initCountUps();
  initFills();
  initShortcuts();
  initMobileNav();
  initViewTransitions();
  // Tooltips can wait until the browser idles — first paint stays lean.
  const w = window as unknown as { requestIdleCallback?: (cb: () => void, o?: { timeout: number }) => void };
  if (typeof w.requestIdleCallback === 'function') w.requestIdleCallback(() => void bindTips(), { timeout: 3000 });
  else window.setTimeout(() => void bindTips(), 1500);
  void import('./effects').then((m) => m.initEffects()).catch(() => {});
}

function initFills(): void {
  const paint = () => {
    Array.from(document.querySelectorAll<HTMLElement>('.xp-fill[data-w], .bar-fill[data-w]')).forEach((f) => {
      const w = f.dataset.w || '0';
      requestAnimationFrame(() => requestAnimationFrame(() => {
        f.style.width = `${w}%`;
      }));
    });
  };
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', paint, { once: true });
  } else {
    paint();
  }
}
