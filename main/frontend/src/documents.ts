import * as pdfjsLib from 'pdfjs-dist';
import pdfWorker from 'pdfjs-dist/build/pdf.worker.min.mjs?url';
import Fuse from 'fuse.js';
import { api, qs, qsa } from './api';
import { toast } from './ui';
import { pop, flash } from './fx';

const PDFJS_CDN_WORKER = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.worker.min.mjs';
try {
  pdfjsLib.GlobalWorkerOptions.workerSrc =
    typeof window !== 'undefined' && (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')
      ? pdfWorker
      : PDFJS_CDN_WORKER;
} catch {
  pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_CDN_WORKER;
}

interface DocExtra {
  id: number;
  title: string;
  subject_id?: number;
  kind: string;
}

export function init(): void {
  initViewer();
  initTags();
  initRename();
  initSearch();
}

// ---------------- PDF.js viewer ----------------
let pdfDoc: any = null;
let pdfPage = 1;
let pdfId = 0;
const SCALE = 1.25;

function initViewer(): void {
  const dialog = qs<HTMLDialogElement>('#pdf-dialog');
  if (!dialog) return;
  const canvas = qs<HTMLCanvasElement>('#pdf-canvas');
  const pageLabel = qs<HTMLElement>('#pdf-page');
  const titleLabel = qs<HTMLElement>('#pdf-title');

  qsa<HTMLButtonElement>('.doc-view').forEach((btn) => {
    btn.addEventListener('click', async () => {
      pop(btn);
      pdfId = Number(btn.dataset.id);
      pdfPage = 1;
      pdfDoc = null;
      dialog.showModal();
      canvas?.classList.add('is-loading');
      try {
        pdfDoc = await pdfjsLib.getDocument({
          url: `${(window as any).GATE_BASE}/documents/stream/${pdfId}`,
        }).promise;
        titleLabel!.textContent = btn.dataset.title || 'Document';
        renderPage();
      } catch (e) {
        canvas?.classList.remove('is-loading');
        toast('Could not load PDF: ' + String(e), 'error');
        dialog.close();
      }
    });
  });

  async function renderPage(): Promise<void> {
    if (!pdfDoc || !canvas) return;
    const page = await pdfDoc.getPage(pdfPage);
    const vp = page.getViewport({ scale: SCALE });
    canvas.width = vp.width;
    canvas.height = vp.height;
    const ctx = canvas.getContext('2d')!;
    await page.render({ canvasContext: ctx, viewport: vp }).promise;
    pageLabel!.textContent = `${pdfPage} / ${pdfDoc.numPages}`;
    canvas.classList.remove('is-loading');
    canvas.classList.remove('anim-fade-up');
    void canvas.offsetWidth;
    canvas.classList.add('anim-fade-up');
  }

  const next = qs<HTMLButtonElement>('#pdf-next');
  const prev = qs<HTMLButtonElement>('#pdf-prev');
  const close = qs<HTMLButtonElement>('#pdf-close');
  next?.addEventListener('click', () => { if (pdfDoc && pdfPage < pdfDoc.numPages) { pdfPage++; renderPage(); } });
  prev?.addEventListener('click', () => { if (pdfPage > 1) { pdfPage--; renderPage(); } });
  close?.addEventListener('click', () => dialog.close());
}

// ---------------- tagging & rename ----------------
function initTags(): void {
  qsa<HTMLSelectElement>('.doc-tag').forEach((sel) => {
    sel.addEventListener('change', async () => {
      try {
        await api(`/api/documents/${sel.dataset.id}/tag`, {
          method: 'POST',
          body: JSON.stringify({ subject_id: Number(sel.value || 0) }),
        });
        toast('Tag saved', 'success');
        const label = qs<HTMLElement>(`#doc-subject-${sel.dataset.id}`);
        if (label) {
          label.textContent = sel.selectedOptions[0]?.textContent || 'Uncategorised';
          flash(label, true);
        }
        const card = sel.closest<HTMLElement>('.doc-card');
        if (card) {
          card.dataset.subject = sel.value;
        }
      } catch (e) {
        toast(String(e), 'error');
      }
    });
  });
}

function initRename(): void {
  qsa<HTMLButtonElement>('.doc-rename').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const title = prompt('New document title:', btn.dataset.title || '');
      if (!title) return;
      try {
        await api('/api/documents/rename', {
          method: 'POST',
          body: JSON.stringify({ id: Number(btn.dataset.id), title }),
        });
        toast('Document renamed', 'success');
        btn.dataset.title = title;
        const card = btn.closest<HTMLElement>('.doc-card');
        const link = card?.querySelector<HTMLAnchorElement>('.doc-card-title');
        if (link) {
          link.textContent = title;
          flash(link, true);
        }
      } catch (e) {
        toast(String(e), 'error');
      }
    });
  });
}

// ---------------- search & filter ----------------
function initSearch(): void {
  const input = qs<HTMLInputElement>('#doc-search');
  const filter = qs<HTMLSelectElement>('#doc-filter');
  const cards = qsa<HTMLElement>('.doc-card');
  const emptyNotice = qs<HTMLElement>('#doc-empty');
  if (!input || !filter) return;

  const list: DocExtra[] = cards.map((c) => ({
    id: Number(c.dataset.docid || 0),
    subject_id: Number(c.dataset.subject || 0),
    title: c.dataset.search || '',
    kind: c.dataset.kind || '',
  }));
  const fuse = new Fuse(list, { keys: ['title'], threshold: 0.35 });

  const apply = () => {
    const q = input.value.trim();
    const f = filter.value;
    let showIds: Set<number> | null = null;
    if (q) {
      showIds = new Set(fuse.search(q).map((r) => r.item.id));
    }
    let visibleCount = 0;
    cards.forEach((c) => {
      let show = true;
      if (f === 'notion') show = c.dataset.kind === 'notion';
      else if (f) show = c.dataset.subject === f;
      if (showIds) show = show && showIds.has(Number(c.dataset.docid || 0));
      c.style.display = show ? '' : 'none';
      if (show) visibleCount++;
    });
    if (emptyNotice) {
      emptyNotice.classList.toggle('hidden', visibleCount > 0);
    }
  };
  input.addEventListener('input', apply);
  filter.addEventListener('change', apply);
}