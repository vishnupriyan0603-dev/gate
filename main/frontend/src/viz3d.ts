import * as THREE from 'three';

// 3D "mastery constellation" for the dashboard: one glowing planet per subject,
// sized + colored by live progress. Click a planet to practice that subject.
// Optional: drop a hero.glb into public/models/ to show it as the centerpiece.
interface SubjectNode {
  name: string;
  pct: number;
  sid: number;
  href: string;
}

function readSubjects(): SubjectNode[] {
  const cards = Array.from(document.querySelectorAll<HTMLElement>('#subject-grid > div'));
  const seen = new Set<number>();
  return cards
    .map((card) => {
      const pct = Number(
        card.querySelector('.bar-fill')?.getAttribute('data-w')
        || card.querySelector('.progress-ring')?.getAttribute('data-pct')
        || '0');
      const name = card.querySelector('p[title]')?.getAttribute('title')?.trim()
        || card.querySelector('p.truncate')?.textContent?.trim()
        || 'Subject';
      const sid = Number(card.getAttribute('data-subject-id') || '0')
        || Number(card.querySelector<HTMLAnchorElement>('a[href*="training?subject="]')?.href.match(/subject=(\d+)/)?.[1] || '0');
      const href = card.querySelector<HTMLAnchorElement>('a[href*="training?subject="]')?.href || '';
      return { name, pct: Number.isFinite(pct) ? pct : 0, sid, href };
    })
    .filter((s) => s.sid > 0 && !seen.has(s.sid) && (seen.add(s.sid), true));
}

function planetColor(pct: number): number {
  if (pct >= 70) return 0x10b981;
  if (pct >= 50) return 0xf59e0b;
  return 0xf43f5e;
}

export async function initConstellation(host: HTMLElement): Promise<void> {
  // Race-proof: claim synchronously, and always wipe stale canvases/labels —
  // covers back-to-back calls during the hero.glb await gap and stale HMR mounts.
  if (host.dataset.vizInit === '1') {
    const canvases = host.querySelectorAll('canvas');
    if (canvases.length <= 1) return;
  }
  host.dataset.vizInit = '1';
  host.replaceChildren();
  const subjects = readSubjects();
  if (subjects.length === 0) {
    delete host.dataset.vizInit;
    host.innerHTML = '<p class="p-6 text-sm text-slate-500">No subjects yet — sync Notion to build your constellation.</p>';
    return;
  }
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const W = Math.max(280, host.clientWidth || host.parentElement?.clientWidth || 560);
  const H = 320;

  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  renderer.setSize(W, H);
  renderer.domElement.style.display = 'block';
  renderer.domElement.style.width = '100%';
  renderer.domElement.style.height = 'auto';
  renderer.domElement.style.cursor = 'pointer';
  // Final sweep: if a concurrent call already mounted, drop ours and exit.
  if (host.querySelector('canvas')) {
    renderer.dispose();
    return;
  }
  host.append(renderer.domElement);

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(48, W / H, 0.1, 100);
  camera.position.set(0, 3.4, 13.5);
  camera.lookAt(0, 0, 0);

  scene.add(new THREE.AmbientLight(0x8899ff, 0.85));
  const key = new THREE.DirectionalLight(0xffffff, 1.2);
  key.position.set(6, 8, 8);
  scene.add(key);

  const system = new THREE.Group();
  scene.add(system);

  // Single clean orbit ellipse
  const orbit = new THREE.Mesh(
    new THREE.TorusGeometry(6.2, 0.015, 8, 160),
    new THREE.MeshBasicMaterial({ color: 0x4f46e5, transparent: true, opacity: 0.4 }),
  );
  orbit.rotation.x = Math.PI / 2;
  orbit.scale.set(1, 0.42, 1);
  system.add(orbit);

  const raycaster = new THREE.Raycaster();
  const pointer = new THREE.Vector2();
  const meshes: THREE.Mesh[] = [];
  const info = document.getElementById('viz3d-info');
  const setInfo = (s?: SubjectNode) => {
    if (!info) return;
    info.textContent = s ? `${s.name} · ${s.pct}% — click to drill` : 'Hover a planet · Click to drill';
  };

  subjects.forEach((s, i) => {
    // Professional planetarium: wide flat ellipse, no vertical scatter
    const a = (i / subjects.length) * Math.PI * 2;
    const R = 6.2;
    const size = 0.42 + (s.pct / 100) * 0.85;
    const mat = new THREE.MeshStandardMaterial({
      color: planetColor(s.pct),
      emissive: planetColor(s.pct),
      emissiveIntensity: 0.45,
      roughness: 0.4,
      metalness: 0.5,
    });
    const mesh = new THREE.Mesh(new THREE.SphereGeometry(size, 40, 40), mat);
    mesh.position.set(Math.cos(a) * R, 0, Math.sin(a) * R * 0.42);
    mesh.userData = { sid: s.sid, href: s.href, baseY: 0, phase: i * 1.7, node: s };
    system.add(mesh);
    meshes.push(mesh);
  });

  // Hover + click
  let hovered: THREE.Object3D | null = null;
  const pick = (cx: number, cy: number): THREE.Object3D | null => {
    const rect = renderer.domElement.getBoundingClientRect();
    pointer.x = ((cx - rect.left) / rect.width) * 2 - 1;
    pointer.y = -((cy - rect.top) / rect.height) * 2 + 1;
    raycaster.setFromCamera(pointer, camera);
    const hit = raycaster.intersectObjects(meshes, false)[0];
    return hit ? hit.object : null;
  };
  renderer.domElement.addEventListener('pointermove', (e) => {
    hovered = pick(e.clientX, e.clientY);
    renderer.domElement.style.cursor = hovered ? 'pointer' : 'default';
    const node = (hovered?.userData as { node?: SubjectNode } | undefined)?.node;
    setInfo(node);
  });
  renderer.domElement.addEventListener('pointerleave', () => {
    hovered = null;
    setInfo(undefined);
  });
  renderer.domElement.addEventListener('click', (e) => {
    const hit = pick(e.clientX, e.clientY);
    const href = (hit?.userData as { href?: string } | undefined)?.href;
    if (href) location.href = href;
  });

  const t0 = performance.now();
  const animate = (t: number) => {
    const el = (t - t0) / 1000;
    system.rotation.y += 0.0012;
    for (const m of meshes) {
      const u = m.userData as { baseY: number; phase: number };
      m.position.y = u.baseY + Math.sin(el * 0.9 + u.phase) * 0.12;
      const s = m === hovered ? 1.25 : 1;
      m.scale.setScalar(m.scale.x + (s - m.scale.x) * 0.15);
    }
    renderer.render(scene, camera);
    if (!reduced) requestAnimationFrame(animate);
  };
  renderer.render(scene, camera);
  if (!reduced) requestAnimationFrame(animate);

  new ResizeObserver(() => {
    const w = Math.max(280, host.clientWidth || W);
    renderer.setSize(w, H);
    camera.aspect = w / H;
    camera.updateProjectionMatrix();
  }).observe(host);
}
