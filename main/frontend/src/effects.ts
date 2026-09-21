import { reducedMotion } from './fx';

let started = false;

export function fxEnabled(): boolean {
  try {
    return localStorage.getItem('gate_fx') !== 'off';
  } catch {
    return true;
  }
}

export function initEffects(): void {
  if (started) return;
  started = true;
  // Interactive shell first; heavy motion loads when the browser idles.
  initTilt();
  initClickFx();
  initLotties();
  initSidebarFx();
  initTyped();
  initPanelToolbox();
  void import('./lottie-fly').then((m) => m.initLottieFly()).catch(() => {});
  void import('./hero-3d').then((m) => m.initHero3d()).catch(() => {});
  if (reducedMotion() || !fxEnabled()) return;
  const idle = (fn: () => void) => {
    const w = window as unknown as { requestIdleCallback?: (cb: () => void, o?: { timeout: number }) => void };
    if (typeof w.requestIdleCallback === 'function') w.requestIdleCallback(fn, { timeout: 2500 });
    else window.setTimeout(fn, 1200);
  };
  idle(() => {
    void initSmooth();
    void initGsapReveals();
    void initParticles();
    void initSplitTitles();
    void initAutoLists();
    void initSvgDraw();
  });
}

function smallScreen(): boolean {
  return window.matchMedia('(max-width: 768px)').matches;
}

// ---------- Lenis smooth scroll (lazy, synced to ScrollTrigger) ----------
async function initSmooth(): Promise<void> {
  if (smallScreen()) return;
  try {
    const [{ default: Lenis }, { gsap }, { ScrollTrigger }] = await Promise.all([
      import('lenis'),
      import('gsap'),
      import('gsap/ScrollTrigger'),
    ]);
    gsap.registerPlugin(ScrollTrigger);
    const lenis = new Lenis({ lerp: 0.11, smoothWheel: true });
    document.documentElement.classList.add('lenis', 'lenis-smooth');
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((t) => lenis.raf(t * 1000));
    gsap.ticker.lagSmoothing(0);
  } catch {
    /* smooth scroll optional — falls back to native + CSS reveals */
    try {
      const { default: Lenis } = await import('lenis');
      const lenis = new Lenis({ lerp: 0.11, smoothWheel: true });
      const raf = (t: number) => {
        lenis.raf(t);
        requestAnimationFrame(raf);
      };
      requestAnimationFrame(raf);
    } catch {
      /* ignore */
    }
  }
}

// ---------- GSAP scroll reveals + hero timeline (lazy) ----------
async function initGsapReveals(): Promise<void> {
  try {
    const [{ gsap }, { ScrollTrigger }] = await Promise.all([
      import('gsap'),
      import('gsap/ScrollTrigger'),
    ]);
    gsap.registerPlugin(ScrollTrigger);
    // Hero timeline: quest hero children rise in sequence
    const hero = document.querySelector<HTMLElement>('.quest-hero');
    if (hero) {
      gsap.fromTo(
        hero,
        { y: 26, opacity: 0, scale: 0.985 },
        { y: 0, opacity: 1, scale: 1, duration: 0.8, ease: 'power3.out' },
      );
      const items = hero.querySelectorAll<HTMLElement>('[data-hero-item]');
      if (items.length) {
        gsap.fromTo(
          items,
          { y: 18, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.6, stagger: 0.08, ease: 'power3.out', delay: 0.15 },
        );
      }
    }
    // Batch reveals for grids/cards
    const batch = Array.from(document.querySelectorAll<HTMLElement>('[data-reveal]'));
    if (batch.length) {
      gsap.set(batch, { y: 22, opacity: 0 });
      ScrollTrigger.batch(batch, {
        start: 'top 92%',
        once: true,
        onEnter: (els) => {
          gsap.to(els as HTMLElement[], {
            y: 0,
            opacity: 1,
            duration: 0.7,
            stagger: 0.07,
            ease: 'power3.out',
            overwrite: true,
          });
        },
      });
    }
    // Animated counters via GSAP ticker
    document.querySelectorAll<HTMLElement>('[data-count-gsap]').forEach((elm) => {
      const target = Number(elm.dataset.countGsap || elm.textContent || '0');
      if (!Number.isFinite(target)) return;
      const obj = { v: 0 };
      ScrollTrigger.create({
        trigger: elm,
        start: 'top 90%',
        once: true,
        onEnter: () => {
          gsap.to(obj, {
            v: target,
            duration: 1.4,
            ease: 'power2.out',
            onUpdate: () => {
              elm.textContent = String(Math.round(obj.v));
            },
          });
        },
      });
    });
    // Safety net: rescue in-viewport elements whose trigger misfired
    // (font shifts, late layouts). Below-fold items keep their animation.
    window.setTimeout(() => {
      const vh = window.innerHeight || 800;
      document.querySelectorAll<HTMLElement>('[data-reveal]').forEach((elm) => {
        const r = elm.getBoundingClientRect();
        const inView = r.top < vh * 0.94 && r.bottom > 0;
        if (inView && (elm.style.opacity === '0' || elm.style.opacity === '')) {
          elm.style.opacity = '1';
          elm.style.transform = 'none';
        }
      });
      import('gsap/ScrollTrigger')
        .then(({ ScrollTrigger: ST }) => ST.refresh())
        .catch(() => {});
    }, 2600);
  } catch {
    /* gsap optional — CSS reveals still work */
  }
}

// ---------- tsParticles hero/cards (lazy, slim, capped) ----------
async function initParticles(): Promise<void> {
  const hosts = Array.from(document.querySelectorAll<HTMLElement>('[data-particles]'));
  if (hosts.length === 0) return;
  if (smallScreen() && !hosts.some((h) => h.id === 'particles-hero')) return;
  try {
    const [{ tsParticles }, { loadSlim }] = await Promise.all([
      import('@tsparticles/engine'),
      import('@tsparticles/slim'),
    ]);
    await loadSlim(tsParticles);
    for (const host of hosts) {
      if (host.dataset.particlesDone) continue;
      host.dataset.particlesDone = '1';
      if (!host.id) host.id = `particles-${Math.random().toString(36).slice(2, 8)}`;
      const n = host.id === 'particles-hero' ? 42 : 22;
      await tsParticles.load({
        id: host.id || undefined,
        element: host,
        options: {
          fullScreen: false,
          fpsLimit: 50,
          particles: {
            number: { value: n },
            size: { value: { min: 1, max: 3 } },
            opacity: { value: { min: 0.25, max: 0.7 } },
            color: { value: ['#a855f7', '#22d3ee', '#f59e0b'] },
            move: { enable: true, speed: 0.5, direction: 'top', outModes: 'out' },
            links: { enable: true, distance: 110, opacity: 0.25, color: '#7c3aed' },
          },
          interactivity: {
            events: { onHover: { enable: true, mode: 'grab' } },
            modes: { grab: { distance: 120, links: { opacity: 0.5 } } },
          },
          detectRetina: true,
        },
      });
    }
    // Pause when tab hidden
    document.addEventListener('visibilitychange', () => {
      const c = (window as unknown as { tsParticlesDom?: { pause?: () => void; play?: () => void }[] }).tsParticlesDom;
      if (document.hidden) c?.forEach((i) => i.pause?.());
      else c?.forEach((i) => i.play?.());
    });
  } catch {
    /* particles optional */
  }
}

// ---------- Vanilla card tilt + 3D depth (desktop, pointer only) ----------
function initTilt(): void {
  if (smallScreen() || !window.matchMedia('(pointer: fine)').matches) return;
  const cards = Array.from(document.querySelectorAll<HTMLElement>('[data-tilt]'));
  for (const card of cards) {
    let raf = 0;
    card.style.transformStyle = 'preserve-3d';
    card.addEventListener('pointermove', (e) => {
      cancelAnimationFrame(raf);
      raf = requestAnimationFrame(() => {
        const r = card.getBoundingClientRect();
        const rx = ((e.clientY - r.top) / r.height - 0.5) * -7;
        const ry = ((e.clientX - r.left) / r.width - 0.5) * 9;
        card.style.transform = `perspective(900px) rotateX(${rx}deg) rotateY(${ry}deg) translateY(-3px)`;
        // Queried per-move so dynamically rendered children (quiz options…) join in.
        card.querySelectorAll<HTMLElement>('[data-depth]').forEach((d, i) => {
          d.style.transform = `translateZ(${26 + (i % 3) * 8}px)`;
        });
      });
    });
    card.addEventListener('pointerleave', () => {
      cancelAnimationFrame(raf);
      card.style.transform = '';
      card.querySelectorAll<HTMLElement>('[data-depth]').forEach((d) => {
        d.style.transform = '';
      });
    });
  }
}

// ---------- Click-fx: springy pop + level-colored burst + drill pulse ----------
// Fires on [data-click-fx] cards; real controls (links/buttons) behave normally.
function initClickFx(): void {
  const fire = (card: HTMLElement) => {
    if (reducedMotion()) return;
    card.classList.remove('anim-pop');
    void card.offsetWidth;
    card.classList.add('anim-pop');
    window.setTimeout(() => card.classList.remove('anim-pop'), 340);
    const lvl =
      getComputedStyle(card).getPropertyValue('--lvl').trim() || '#7c3aed';
    const r = card.getBoundingClientRect();
    void import('canvas-confetti')
      .then(({ default: confetti }) => {
        confetti({
          particleCount: 45,
          spread: 65,
          startVelocity: 28,
          origin: {
            x: (r.left + r.width / 2) / window.innerWidth,
            y: (r.top + r.height / 2) / window.innerHeight,
          },
          colors: [lvl, '#ffffff', '#7c3aed'],
        });
      })
      .catch(() => {});
    const drill = card.querySelector<HTMLElement>('.drill-btn');
    if (drill) {
      drill.classList.remove('anim-pulse-glow');
      void drill.offsetWidth;
      drill.classList.add('anim-pulse-glow');
      window.setTimeout(() => drill.classList.remove('anim-pulse-glow'), 2500);
    }
  };
  document.addEventListener('click', (e) => {
    const t = e.target as HTMLElement | null;
    if (!t) return;
    if (t.closest('a, button, input, select, textarea, dialog')) return;
    const card = t.closest<HTMLElement>('[data-click-fx]');
    if (card) fire(card);
  });
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    const t = e.target as HTMLElement | null;
    const card = t?.closest?.('[data-click-fx]') as HTMLElement | null;
    if (card && (t === card || t?.closest('.mastery-card, .mastery-row'))) {
      e.preventDefault();
      fire(card);
    }
  });
}

// ---------- Lottie subject mascots (lazy, pause offscreen) ----------
const LOTTIE_MAP: Array<[RegExp, string]> = [
  [/operating/i, 'gear'],
  [/dbms|database|sql/i, 'database'],
  [/data structur/i, 'stack'],
  [/network/i, 'network'],
  [/algorithm/i, 'graph'],
  [/toc|theory|automat|computation/i, 'infinity'],
  [/compiler/i, 'code'],
  [/coa|architect|digital|logic/i, 'chip'],
  [/aptitude/i, 'target'],
  [/math/i, 'graph'],
];

function lottieFor(name: string): string {
  for (const [re, file] of LOTTIE_MAP) {
    if (re.test(name)) return file;
  }
  return 'star';
}

function initLotties(): void {
  const hosts = Array.from(document.querySelectorAll<HTMLElement>('[data-lottie]'));
  if (hosts.length === 0) return;
  // Letter fallback first — never an empty box, even with fx off.
  hosts.forEach((h) => {
    const name = (h.dataset.lottie || 'S').trim();
    h.textContent = name.charAt(0).toUpperCase();
  });
  if (reducedMotion() || !fxEnabled()) return;
  const base = (window as { GATE_BASE?: string }).GATE_BASE || '';
  let observer: IntersectionObserver | null = null;
  const play = (host: HTMLElement, anim: { play?: () => void; pause?: () => void }) => {
    if (typeof IntersectionObserver === 'undefined') {
      anim.play?.();
      return;
    }
    if (!observer) {
      observer = new IntersectionObserver(
        (entries) => {
          for (const en of entries) {
            const a = (en.target as HTMLElement & { __lottie?: { play?: () => void; pause?: () => void } }).__lottie;
            if (!a) continue;
            if (en.isIntersecting) a.play?.();
            else a.pause?.();
          }
        },
        { threshold: 0.1 },
      );
    }
    (host as HTMLElement & { __lottie?: unknown }).__lottie = anim;
    observer.observe(host);
  };
  void import('lottie-web')
    .then(({ default: lottie }) => {
      for (const host of hosts) {
        try {
          const anim = lottie.loadAnimation({
            container: host,
            renderer: 'svg',
            loop: true,
            autoplay: false,
            path: `${base}/lottie/${lottieFor(host.dataset.lottie || '')}.json`,
          });
          host.textContent = '';
          play(host, anim);
        } catch {
          /* letter fallback stays */
        }
      }
    })
    .catch(() => {
      /* letter fallback stays */
    });
}

// ---------- Sidebar drawer / letter jumps (collapse + fx toggle retired) ----------
function initSidebarFx(): void {
  const body = document.body;
  // Collapse control removed: never restore a stuck mini sidebar; clear legacy key.
  body.classList.remove('side-mini');
  try {
    localStorage.removeItem('gate_side');
  } catch {
    /* ignore */
  }
  document.getElementById('side-open')?.addEventListener('click', () => body.classList.add('side-open'));
  document.getElementById('side-scrim')?.addEventListener('click', () => body.classList.remove('side-open'));
  // Letter jumps: g c t h p l d s (like Gmail for study)
  const jumps: Record<string, string> = {
    g: '/dashboard',
    c: '/course',
    t: '/training',
    h: '/challenge',
    p: '/performance',
    l: '/calendar',
    d: '/documents',
    s: '/settings',
  };
  document.addEventListener('keydown', (e) => {
    const a = document.activeElement as HTMLElement | null;
    const typing = !!a && (a.tagName === 'INPUT' || a.tagName === 'TEXTAREA' || a.tagName === 'SELECT' || a.isContentEditable);
    if (typing || e.metaKey || e.ctrlKey || e.altKey) return;
    const k = e.key.toLowerCase();
    if (jumps[k]) {
      const base = (window as { GATE_BASE?: string }).GATE_BASE || '';
      location.href = `${base}${jumps[k]}`;
    }
  });
}

// ---------- Gentelella panel toolbox: collapse toggle (vanilla, no jQuery) ----------
function initPanelToolbox(): void {
  document.addEventListener('click', (e) => {
    const btn = (e.target as HTMLElement | null)?.closest?.('[data-panel-collapse]') as HTMLElement | null;
    if (!btn) return;
    const panel = btn.closest('.x-panel');
    if (panel) panel.classList.toggle('collapsed');
  });
}

// ---------- Typed quest line (dependency-free typewriter) ----------
function initTyped(): void {
  const elm = document.querySelector<HTMLElement>('[data-typed]');
  if (!elm) return;
  const lines = (elm.dataset.typed || '').split('||').map((s) => s.trim()).filter(Boolean);
  if (lines.length === 0) return;
  let li = 0;
  let ci = 0;
  let del = false;
  const tick = () => {
    const line = lines[li];
    elm.textContent = line.slice(0, ci) + '▍';
    if (!del) {
      ci++;
      if (ci > line.length + 14) del = true;
    } else {
      ci -= 2;
      if (ci <= 0) {
        del = false;
        li = (li + 1) % lines.length;
        ci = 0;
      }
    }
    window.setTimeout(tick, del ? 28 : 55);
  };
  tick();
}

// ---------- Split-type hero titles (lazy, char stagger) ----------
async function initSplitTitles(): Promise<void> {
  const heads = Array.from(document.querySelectorAll<HTMLElement>('[data-split]:not([data-split-done])'));
  if (heads.length === 0) return;
  try {
    const [{ default: SplitType }, { gsap }] = await Promise.all([import('split-type'), import('gsap')]);
    for (const h of heads) {
      h.dataset.splitDone = '1';
      const split = new SplitType(h, { types: 'chars' }) as unknown as { chars?: HTMLElement[] };
      if (split.chars && split.chars.length > 0) {
        gsap.from(split.chars, { y: 18, opacity: 0, duration: 0.5, stagger: 0.02, ease: 'power3.out', delay: 0.1 });
      }
    }
  } catch {
    heads.forEach((h) => { h.dataset.splitDone = '1'; });
  }
}

// ---------- Auto-animate filterable lists (tiny, lazy) ----------
async function initAutoLists(): Promise<void> {
  const lists = Array.from(document.querySelectorAll<HTMLElement>('#mcq-list, #doc-grid, #checklists'));
  if (lists.length === 0) return;
  try {
    const { default: autoAnimate } = await import('@formkit/auto-animate');
    lists.forEach((l) => autoAnimate(l, { duration: 250 }));
  } catch {
    /* lists work without animation */
  }
}

// ---------- SVG stroke draw + motion-path rocket (GSAP, free plugins) ----------
async function initSvgDraw(): Promise<void> {
  const paths = Array.from(document.querySelectorAll<SVGPathElement>('[data-svg-draw]:not([data-draw-done])'));
  const movers = Array.from(document.querySelectorAll<HTMLElement>('[data-motion-path]:not([data-motion-done])'));
  if (paths.length === 0 && movers.length === 0) return;
  try {
    const [{ gsap }, { ScrollTrigger }, { MotionPathPlugin }] = await Promise.all([
      import('gsap'),
      import('gsap/ScrollTrigger'),
      import('gsap/MotionPathPlugin'),
    ]);
    gsap.registerPlugin(ScrollTrigger, MotionPathPlugin);
    for (const p of paths) {
      p.dataset.drawDone = '1';
      const len = (p as SVGPathElement).getTotalLength?.() || 600;
      gsap.fromTo(p, { strokeDasharray: len, strokeDashoffset: len }, {
        strokeDashoffset: 0, duration: 1.4, ease: 'power2.out',
        scrollTrigger: { trigger: p.closest('[data-hero], section, div') || p, start: 'top 88%', once: true },
      });
    }
    for (const m of movers) {
      const track = m.dataset.motionPath || '';
      const target = track ? document.querySelector<SVGPathElement>(track) : null;
      if (!target) { (m as HTMLElement).dataset.motionDone = '1'; continue; }
      (m as HTMLElement).dataset.motionDone = '1';
      gsap.to(m, { motionPath: { path: target, align: target, alignOrigin: [0.5, 0.5] }, duration: 6, repeat: -1, ease: 'none' });
    }
  } catch {
    [...paths, ...movers].forEach((e) => { (e as HTMLElement).dataset.drawDone = '1'; });
  }
}
