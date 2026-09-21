// 3D hero via @google/model-viewer (lazy). Usage:
// <model-viewer data-glb src=".../models/hero.glb" poster=".../media/poster.webp"
//   auto-rotate camera-controls ar style="..."></model-viewer>
// Skipped on reduced-motion / fx-off / small screens; missing GLB falls back silently.
import { reducedMotion } from './fx';

function fxOff(): boolean {
  try {
    return localStorage.getItem('gate_fx') === 'off';
  } catch {
    return false;
  }
}

function smallScreen(): boolean {
  return window.matchMedia('(max-width: 768px)').matches;
}

export function initHero3d(root: ParentNode = document): void {
  const hosts = Array.from(root.querySelectorAll<HTMLElement>('[data-glb]:not([data-glb-done])'));
  if (hosts.length === 0) return;
  if (reducedMotion() || fxOff() || smallScreen()) return;
  const io =
    typeof IntersectionObserver !== 'undefined'
      ? new IntersectionObserver(
          (entries) => {
            for (const en of entries) {
              if (en.isIntersecting) {
                io?.disconnect();
                void import('@google/model-viewer').catch(() => {});
              }
            }
          },
          { threshold: 0.1 },
        )
      : null;
  hosts.forEach((h) => {
    h.dataset.glbDone = '1';
    if (io) io.observe(h);
    else void import('@google/model-viewer').catch(() => {});
  });
}
