<?php $base = rtrim(base_url(), '/'); ?>
<div class="space-y-6">
  <!-- Header & Controls -->
  <section class="quest-hero p-5 sm:p-7" data-reveal>
    <div id="particles-hero" data-particles aria-hidden="true"></div>
    <div class="relative z-10 flex flex-wrap items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3" data-split>
        <span class="p-2 rounded-xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400">
          <span data-lucide="folder-kanban" class="w-6 h-6"></span>
        </span>
        <span>Document <span class="text-quest">Hub</span></span>
      </h1>
      <p class="mt-1 text-sm text-slate-400" data-quote>Curated syllabus PDFs, formulas, standard reference books and Notion databases.</p>
    </div>

    <!-- Search & Filter Controls -->
    <div class="flex flex-wrap items-center gap-2.5">
      <div class="relative w-full sm:w-auto">
        <span data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></span>
        <input id="doc-search" data-search-focus class="input input-sm input-bordered pl-9 pr-9 w-full sm:w-60 bg-slate-900/90 border border-white/10 rounded-xl focus:border-indigo-400 text-sm placeholder:text-slate-500" placeholder="Search docs or books…">
        <kbd class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] font-mono text-slate-500 bg-slate-800 px-1.5 py-0.5 rounded border border-slate-700 pointer-events-none">/</kbd>
      </div>

      <select id="doc-filter" class="select select-sm bg-slate-900/90 border border-white/10 text-slate-200 rounded-xl focus:border-indigo-400">
        <option value="">All Subjects & Sources</option>
        <option value="notion">Notion Pages Only</option>
        <?php foreach ($subjects as $s): ?>
        <option value="<?= (int) $s['id'] ?>"><?= esc($s['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    </div>
  </section>

  <!-- Document Grid -->
  <div id="doc-grid" class="masonry">
    <?php foreach ($docs as $i => $d): ?>
      <?php
      $isPdf = $d['kind'] === 'pdf';
      $badgeCls = $isPdf ? 'chip-streak' : 'chip-quest';
      $href = $isPdf
          ? $base . '/documents/stream/' . (int) $d['id']
          : safe_external_url($d['url'] ?? null);
      ?>
      <div class="study-card p-5 doc-card group hov-lift" data-tilt data-reveal style="--d:<?= min($i, 11) * 40 ?>ms" data-docid="<?= (int) $d['id'] ?>" data-subject="<?= (int) ($d['subject_id'] ?? 0) ?>" data-kind="<?= esc($d['kind']) ?>" data-search="<?= esc(trim(($d['title'] ?? '') . ' ' . ($d['subject_name'] ?? ''))) ?>">
        <div>
          <div class="flex items-start justify-between gap-2.5">
            <div class="flex items-center gap-2 min-w-0">
              <span class="p-2 rounded-xl <?= $isPdf ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' ?> shrink-0" data-depth>
                <span data-lucide="<?= $isPdf ? 'file-text' : 'external-link' ?>" class="w-4 h-4"></span>
              </span>
              <a href="<?= esc($href) ?>" target="_blank" rel="noopener" class="doc-card-title text-sm font-bold text-white group-hover:text-indigo-300 transition-colors line-clamp-2">
                <?= esc($d['title']) ?>
              </a>
            </div>
            <span class="chip <?= $badgeCls ?> text-[10px] shrink-0 uppercase font-mono tracking-wider">
              <?= $isPdf ? 'PDF' : 'LINK' ?>
            </span>
          </div>

          <div class="mt-3 flex items-center gap-1.5 text-xs text-slate-400">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
            <span id="doc-subject-<?= (int) $d['id'] ?>" class="truncate font-medium">
              <?= esc($d['subject_name'] ?? 'Uncategorised') ?>
            </span>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between gap-2 text-xs">
          <select class="doc-tag select select-xs bg-slate-800/80 border border-white/10 rounded-lg max-w-[130px] text-[11px]" data-id="<?= (int) $d['id'] ?>">
            <option value="">— assign subject —</option>
            <?php foreach ($subjects as $s): ?>
            <option value="<?= (int) $s['id'] ?>" <?= (int) $s['id'] === (int) ($d['subject_id'] ?? 0) ? 'selected' : '' ?>><?= esc($s['name']) ?></option>
            <?php endforeach; ?>
          </select>

          <div class="flex items-center gap-1.5">
            <?php if ($isPdf): ?>
              <button class="doc-view btn btn-xs btn-quest rounded-lg" data-id="<?= (int) $d['id'] ?>" data-title="<?= esc($d['title'], 'attr') ?>" title="View in modal">
                <span data-lucide="eye" class="w-3.5 h-3.5"></span><span>View</span>
              </button>
              <button class="doc-rename btn btn-xs btn-ghost border border-white/10 rounded-lg" data-id="<?= (int) $d['id'] ?>" data-title="<?= esc($d['title'], 'attr') ?>" title="Rename document">
                <span data-lucide="edit-3" class="w-3.5 h-3.5"></span><span>Rename</span>
              </button>
            <?php else: ?>
              <a href="<?= esc($href) ?>" target="_blank" rel="noopener" class="btn btn-xs btn-quest rounded-lg" title="Open Link">
                <span data-lucide="external-link" class="w-3.5 h-3.5"></span><span>Open</span>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div id="doc-empty" class="hidden study-card p-12 text-center">
    <div class="w-12 h-12 mx-auto" data-lottie-fly="empty" aria-hidden="true"></div>
    <p class="text-sm text-slate-300 font-medium">No documents match your query.</p>
    <p class="text-xs text-slate-500 mt-1">Try another keyword or select "All Subjects".</p>
  </div>

  <?php if (count($docs) === 0): ?>
    <div class="study-card p-12 text-center">
      <span data-lucide="folder-kanban" class="w-8 h-8 mx-auto text-slate-600 mb-2"></span>
      <p class="text-sm text-slate-400">No documents registered yet. Sync your Notion workspace or upload PDFs into the study directory.</p>
    </div>
  <?php endif; ?>
</div>

<!-- PDF viewer dialog -->
<dialog id="pdf-dialog" class="modal modal-middle bg-slate-950/85 backdrop-blur-md">
  <div class="modal-box max-w-5xl h-[85vh] bg-slate-900/95 border border-white/10 rounded-2xl shadow-2xl flex flex-col p-5">
    <div class="flex items-center justify-between border-b border-white/10 pb-4">
      <div class="flex items-center gap-2.5 min-w-0 pr-4">
        <span class="p-1.5 rounded-lg bg-rose-500/10 text-rose-400 border border-rose-500/20 shrink-0">
          <span data-lucide="file-text" class="w-4 h-4"></span>
        </span>
        <h3 id="pdf-title" class="font-bold text-white text-base truncate">Document Preview</h3>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button id="pdf-prev" class="btn btn-xs btn-outline border-white/10 text-slate-300 hover:text-white px-2">‹ Previous</button>
        <span id="pdf-page" class="text-xs font-mono text-slate-400 bg-slate-800/80 px-2 py-1 rounded-md border border-white/5">1/1</span>
        <button id="pdf-next" class="btn btn-xs btn-outline border-white/10 text-slate-300 hover:text-white px-2">Next ›</button>
        <button id="pdf-close" class="btn btn-xs btn-error bg-rose-600 hover:bg-rose-500 border-rose-500 text-white ml-2">✕ Close</button>
      </div>
    </div>
    <div class="mt-4 flex-1 overflow-auto rounded-xl bg-slate-950/80 border border-white/5 p-4 flex items-start justify-center">
      <canvas id="pdf-canvas" class="max-w-full rounded-lg shadow-2xl bg-white"></canvas>
    </div>
  </div>
</dialog>
