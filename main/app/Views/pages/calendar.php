<div class="space-y-6">
  <!-- Calendar Header & Controls -->
  <div class="quest-hero p-6 flex flex-wrap items-center justify-between gap-4" data-reveal>
    <div id="particles-hero" data-particles aria-hidden="true"></div>
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
        <span class="p-2 rounded-xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400">
          <span data-lucide="calendar-days" class="w-6 h-6"></span>
        </span>
        <span>GATE CS 2027 <span class="text-quest">Day-wise Schedule</span></span>
      </h1>
      <p class="mt-1 text-sm text-slate-300">
        <span class="font-mono text-indigo-300 font-bold"><?= (int) $count ?></span> scheduled sessions · 
        <span class="font-mono text-emerald-400 font-bold"><?= (int) $done ?></span> completed (<?= $count ? round(($done / $count) * 100) : 0 ?>%) · 
        <span class="text-slate-400">Direct 2-way sync with your Notion workspace.</span>
      </p>
      <div class="flex flex-nowrap items-center gap-2 mt-2 overflow-x-auto pb-1 -mx-1 px-1" data-carousel>
        <span class="text-[11px] font-mono text-slate-400">Jump to Month:</span>
        <button type="button" class="btn btn-xs rounded-lg border border-indigo-500/40 bg-indigo-600 text-white font-semibold" data-month-jump="2026-09-17">Sep '26</button>
        <button type="button" class="btn btn-xs rounded-lg border border-white/10 bg-slate-800/80 text-slate-300 hover:text-white" data-month-jump="2026-10-01">Oct '26</button>
        <button type="button" class="btn btn-xs rounded-lg border border-white/10 bg-slate-800/80 text-slate-300 hover:text-white" data-month-jump="2026-11-01">Nov '26</button>
        <button type="button" class="btn btn-xs rounded-lg border border-white/10 bg-slate-800/80 text-slate-300 hover:text-white" data-month-jump="2026-12-01">Dec '26</button>
        <button type="button" class="btn btn-xs rounded-lg border border-white/10 bg-slate-800/80 text-slate-300 hover:text-white" data-month-jump="2027-01-01">Jan '27</button>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
      <!-- Sync Notion button -->
      <button id="btn-notion-sync-cal" class="btn btn-sm border border-amber-500/40 bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 gap-2 font-semibold hov-lift">
        <span data-lucide="refresh-cw" class="w-3.5 h-3.5"></span>
        <span>Sync Notion</span>
      </button>

      <!-- Status Legend -->
      <div class="flex flex-wrap items-center gap-2.5 bg-slate-900/90 border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300">
        <span class="flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
          <span>Done</span>
        </span>
        <span class="w-px h-3 bg-slate-700"></span>
        <span class="flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
          <span>Scheduled</span>
        </span>
        <span class="w-px h-3 bg-slate-700"></span>
        <span class="flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-rose-500"></span>
          <span>6h Mock</span>
        </span>
      </div>

      <a href="<?= esc($baseUrl) ?>/training" class="btn btn-sm btn-primary bg-indigo-600 hover:bg-indigo-500 border-indigo-500 text-white shadow-lg shadow-indigo-950/50 gap-2 font-semibold">
        <span data-lucide="zap" class="w-4 h-4"></span>
        <span>Training Arena</span>
      </a>
    </div>
  </div>

  <!-- Interactive Calendar Canvas + agenda rail -->
  <div class="mosaic">
    <section class="study-card x-panel span-8 p-5" data-reveal data-tilt style="--d:.08s">
      <div class="x-title">
        <div class="flex items-center gap-2">
          <span data-lucide="calendar-days" class="h-4 w-4 text-indigo-400"></span>
          <h2>Month View</h2>
        </div>
        <span class="text-[11px] font-mono text-slate-400">IST · DD-MM-YYYY</span>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content"><div id="calendar"></div></div>
    </section>
    <aside class="span-4 grid content-start gap-5" data-reveal style="--d:.12s">
      <div class="study-card x-panel p-5" id="day-panel" data-tilt>
        <div class="x-title">
          <h2>Day Detail</h2>
          <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
        </div>
        <div class="x-content">
        <div id="day-front">
          <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Roadmap Progress · Sep 2026 - Jan 2027</p>
          <p class="mt-1 text-2xl font-extrabold text-white">
            <span class="text-emerald-400"><?= (int) $done ?></span>
            <span class="text-sm font-normal text-slate-400"> / <?= (int) $count ?> days finished</span>
          </p>
          <div class="bar-track mt-2.5 h-2">
            <div class="bar-fill h-full rounded-full bg-gradient-to-r from-emerald-500 via-indigo-500 to-cyan-400" data-w="<?= $count ? round(($done / $count) * 100) : 0 ?>"></div>
          </div>
          <p class="mt-3 text-center font-mono text-[10px] uppercase tracking-widest text-slate-500">Click any date on the calendar to view details & toggle status ↓</p>
        </div>
        <div id="day-detail" class="hidden space-y-2.5">
          <div class="flex items-center justify-between">
            <p class="text-[11px] font-bold uppercase tracking-wider text-cyan-300" id="day-detail-date">Selected day</p>
            <span id="day-detail-pyqs" class="hidden text-[11px] font-mono font-bold text-amber-300 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20"></span>
          </div>
          <p class="font-display text-sm font-bold text-white leading-snug" id="day-detail-task">Pick any scheduled day.</p>
          <p id="day-detail-note" class="hidden text-xs text-slate-300 bg-slate-800/80 p-2.5 rounded-lg border border-white/5"></p>
          <div class="pt-1" id="day-detail-status">Tap a date on the calendar.</div>
          <button class="btn btn-xs w-full rounded-lg font-semibold" id="day-detail-toggle" type="button">Mark Complete ✓ (+20 XP)</button>
          <div class="flex items-center gap-2 pt-1">
            <a id="day-detail-notion" href="#" target="_blank" class="hidden btn btn-xs flex-1 rounded-lg border border-amber-400/30 bg-amber-500/10 text-amber-300 hover:bg-amber-500/20 text-center">Open in Notion ↗</a>
            <button class="btn btn-xs flex-1 rounded-lg border border-white/10 text-slate-400 hover:text-white" id="day-detail-back" type="button">← Back</button>
          </div>
        </div>
        </div>
      </div>

      <div class="study-card x-panel p-5 hov-lift" data-tilt>
        <div class="x-title">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-300">Live 2-Way Notion Sync</p>
          <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
        </div>
        <div class="x-content">
        <p class="text-xs leading-relaxed text-slate-400">
          Marking tasks as complete automatically pushes status updates to your Notion database. Any edits made in Notion can be refreshed with one click.
        </p>
        </div>
      </div>

      <a href="<?= esc($baseUrl) ?>/training" class="study-card block p-5 hov-glow" data-tilt>
        <p class="text-[11px] font-bold uppercase tracking-wider text-violet-300">Practice Mode</p>
        <p class="mt-1 text-sm font-semibold text-white">Solve PYQs for today's topic →</p>
      </a>
    </aside>
  </div>
</div>
