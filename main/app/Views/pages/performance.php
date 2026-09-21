<div class="space-y-6">
  <!-- Page Header -->
  <section class="quest-hero p-5 sm:p-7" data-reveal>
    <div id="particles-hero" data-particles aria-hidden="true"></div>
    <div class="relative z-10 flex flex-wrap items-center justify-between gap-4">
      <div class="min-w-0">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
          <span class="p-2 rounded-xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 shrink-0">
            <span data-lucide="trending-up" class="w-6 h-6"></span>
          </span>
          <span data-split class="inline-block">Performance <span class="text-quest">Intelligence</span></span>
        </h1>
        <p class="mt-1 text-sm text-slate-400" data-quote>Empirical accuracy trends, diagnostic weak spots and syllabus trajectory toward 80-90 marks.</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="chip chip-quest text-xs">GATE CS 2027</span>
        <span class="chip chip-focus text-xs">Target: Rank &lt; 100</span>
      </div>
    </div>
  </section>

  <!-- Top KPI Summary Cards (Gentelella tile_count) -->
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal style="--d:.04s">
    <div class="study-card tile-stats p-5 border border-white/10 hov-lift" data-tilt>
      <span class="t-icon" aria-hidden="true"><span data-lucide="target"></span></span>
      <div class="t-count">80-90 <span class="text-sm font-normal text-slate-400">/ 100</span></div>
      <h2>Target Score</h2>
      <span class="t-foot"><span class="up">▲</span> On track for GATE 2027</span>
    </div>

    <div class="study-card tile-stats p-5 border border-white/10 hov-lift" data-tilt>
      <span class="t-icon" aria-hidden="true"><span data-lucide="trophy"></span></span>
      <div class="t-count">&lt; 100 <span class="text-sm font-normal text-amber-400">Top Tier</span></div>
      <h2>AIR Target</h2>
      <span class="t-foot"><span class="up">▲</span> 4% from last mock</span>
    </div>

    <div class="study-card tile-stats p-5 border border-white/10 hov-lift" data-tilt>
      <span class="t-icon" aria-hidden="true"><span data-lucide="flame"></span></span>
      <div class="t-count">18-20 <span class="text-sm font-normal text-slate-400">Hrs/wk</span></div>
      <h2>Weekly Goal</h2>
      <span class="t-foot"><span class="up">▲</span> 3% vs last week</span>
    </div>

    <div class="study-card tile-stats p-5 border border-white/10 hov-lift" data-tilt>
      <span class="t-icon" aria-hidden="true"><span data-lucide="activity"></span></span>
      <div class="t-count">3-Cycle <span class="text-sm font-normal text-indigo-400">Retention</span></div>
      <h2>Revision Metric</h2>
      <span class="t-foot">Spaced repetition active</span>
    </div>
  </div>

  <!-- Primary Charts mosaic -->
  <div class="mosaic">
    <section class="study-card x-panel span-7 p-5" data-reveal data-tilt style="--d:.06s">
      <div class="x-title">
        <div class="flex items-center gap-2">
          <span data-lucide="trending-up" class="w-4 h-4 text-indigo-400"></span>
          <h2>Accuracy Trend Over Time (%)</h2>
        </div>
        <span class="text-[11px] font-mono text-slate-400">All Workouts</span>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content"><div id="chart-accuracy" class="chart-loading mt-3 h-64"></div></div>
    </section>

    <section class="study-card x-panel span-5 p-5" data-reveal data-tilt style="--d:.08s">
      <div class="x-title">
        <div class="flex items-center gap-2">
          <span data-lucide="activity" class="w-4 h-4 text-emerald-400"></span>
          <h2>Subject-Wise Accuracy Comparison</h2>
        </div>
        <span class="text-[11px] font-mono text-slate-400">Target &ge; 75%</span>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content"><div id="chart-subjects" class="chart-loading mt-3 h-64"></div></div>
    </section>

    <section class="study-card x-panel span-5 p-5" data-reveal data-tilt style="--d:.1s">
      <div class="x-title">
        <div class="flex items-center gap-2">
          <span data-lucide="target" class="w-4 h-4 text-amber-400"></span>
          <h2>Performance vs Mock Phase Targets</h2>
        </div>
        <span class="chip chip-xp text-[10px]">Benchmark</span>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content"><div id="chart-targets" class="chart-loading mt-3 h-64"></div></div>
    </section>

    <section class="study-card x-panel span-7 p-5" data-reveal data-tilt style="--d:.12s">
      <div class="x-title">
        <div class="flex items-center gap-2">
          <span data-lucide="calendar-clock" class="w-4 h-4 text-purple-400"></span>
          <h2>Daily Attempt Volume</h2>
        </div>
        <span class="text-[11px] font-mono text-slate-400">Questions Answered</span>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content"><div id="chart-attempts" class="chart-loading mt-3 h-64"></div></div>
    </section>
  </div>

  <!-- Weak Areas Diagnostic Grid -->
  <section class="study-card x-panel p-6 hov-lift" data-reveal data-tilt style="--d:.14s">
    <div class="x-title">
      <div class="flex items-center gap-2.5">
        <div class="p-1.5 rounded-lg bg-amber-500/15 text-amber-400">
          <span data-lucide="brain" class="w-4 h-4"></span>
        </div>
        <div>
          <h2 class="text-base font-bold text-white">Diagnostic Weak Areas & Priority Drills</h2>
          <p class="text-xs text-slate-400 mt-0.5">Automated detection based on lowest MCQ precision in past sessions.</p>
        </div>
      </div>
      <span class="chip chip-xp text-xs">Action Required</span>
      <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
    </div>
    <div class="x-content"><div id="weak-areas" class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3"></div></div></section>

  <!-- Study Hours + Mastery mosaic -->
  <div class="mosaic">
  <section class="study-card x-panel span-7 p-6" data-reveal data-tilt style="--d:.16s">
    <div class="x-title">
      <div class="flex items-center gap-2.5">
        <div class="p-1.5 rounded-lg bg-emerald-500/15 text-emerald-400">
          <span data-lucide="clock" class="w-4 h-4"></span>
        </div>
        <h2 class="text-base font-bold text-white">Study Minutes Logged (Past 90 Days)</h2>
      </div>
      <span class="text-xs font-mono text-slate-400">Daily Tracker</span>
      <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
    </div>
    <div class="x-content"><div id="chart-hours" class="chart-loading mt-4 h-48"></div></div></section>

  <!-- Subject Mastery Status -->
  <section class="study-card x-panel span-5 p-6" data-reveal data-tilt style="--d:.18s">
    <div class="x-title">
      <div class="flex items-center gap-2.5">
        <div class="p-1.5 rounded-lg bg-indigo-500/15 text-indigo-400">
          <span data-lucide="award" class="w-4 h-4"></span>
        </div>
        <h2 class="text-base font-bold text-white">Complete Subject Mastery Index</h2>
      </div>
      <span class="chip chip-quest text-xs">Curriculum Tracking</span>
      <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
    </div>
    <div class="x-content"><div id="mastery" class="mt-4 space-y-5"></div></div></section>
  </div>
</div>
