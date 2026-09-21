<?php $base = rtrim(base_url(), '/'); ?>
<div class="mx-auto max-w-5xl">
<nav class="mb-4 flex items-center gap-1.5 text-xs text-slate-500" aria-label="Breadcrumb" data-reveal>
  <a href="<?= $base ?>/training" class="hover:text-violet-300 transition-colors">Training</a>
  <span aria-hidden="true">›</span><span class="text-slate-300">Daily Challenge</span>
</nav>
<div class="mosaic">
  <!-- Challenge Hero Header -->
  <div class="quest-hero span-7 p-6 relative overflow-hidden" data-reveal data-tilt>
    <div id="particles-hero" data-particles aria-hidden="true"></div>
    <div class="absolute -top-12 -right-12 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="w-16 h-16 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 shadow-[0_0_30px_rgba(245,158,11,0.2)] mb-3" data-depth>
      <span data-lucide="trophy" class="w-8 h-8"></span>
    </div>
    <h1 class="text-3xl font-extrabold text-white tracking-tight" data-split>Daily <span class="text-amber-400">Challenge</span></h1>
    <p class="mt-2 text-sm text-slate-300 max-w-md" data-quote>
      10 GATE-tier questions · Strict 10-minute timer · No instant reveals · Huge bonus reward.
    </p>

    <div class="mt-4 flex flex-wrap items-center gap-2">
      <span class="chip chip-xp text-xs font-mono font-bold">+100 XP Bonus</span>
      <span class="chip chip-quest text-xs">10 Questions</span>
      <span class="chip chip-focus text-xs">Streak Multiplier</span>
    </div>

    <!-- Filter & Launch Controls -->
    <div class="mt-6 flex flex-wrap items-center gap-3">
      <select id="challenge-subject" class="select select-sm bg-slate-900/90 border border-white/10 text-slate-200 rounded-xl focus:border-amber-400">
        <option value="">All subjects (Grand Mock)</option>
        <?php foreach ($subjects as $s): ?>
        <option value="<?= (int) $s['id'] ?>"><?= esc($s['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <select id="challenge-phase" class="select select-sm bg-slate-900/90 border border-white/10 text-slate-200 rounded-xl focus:border-emerald-400">
        <option value="">All phases</option>
        <?php foreach ($phases as $p): ?>
        <option value="<?= (int) $p['id'] ?>"><?= esc($p['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <button id="challenge-start" class="btn btn-sm btn-primary bg-amber-500 hover:bg-amber-400 border-amber-400 text-slate-950 font-bold px-5 shadow-lg shadow-amber-950/50 gap-2">
        <span data-lucide="zap" class="w-4 h-4"></span>
        <span>Commence Challenge</span>
      </button>
    </div>
  </div>

  <!-- Rules rail -->
  <aside class="span-5 grid content-start gap-5" data-reveal style="--d:.05s">
    <div class="study-card x-panel p-5 hov-lift" data-tilt>
      <div class="x-title">
        <p class="text-[11px] font-bold uppercase tracking-wider text-amber-300">Rules of the arena</p>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content">
      <ul class="mt-2 space-y-1.5 text-xs text-slate-400">
        <li>⏱ 10 questions · strict 10-minute timer</li>
        <li>🙈 No instant reveals — answers at the end</li>
        <li>🔥 Streak alive? Score multiplies your glory</li>
      </ul>
      </div>
    </div>
    <div class="study-card x-panel p-5 hov-lift" data-tilt>
      <div class="x-title">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">XP tiers</p>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content">
      <div class="mt-2 space-y-1.5 font-mono text-[11px]">
        <p class="flex justify-between"><span class="text-slate-400">Perfect 10/10</span><span class="text-amber-300 font-bold">+100 XP 🎆</span></p>
        <p class="flex justify-between"><span class="text-slate-400">8–9 correct</span><span class="text-emerald-300 font-bold">+60 XP 🎉</span></p>
        <p class="flex justify-between"><span class="text-slate-400">5–7 correct</span><span class="text-violet-300 font-bold">+30 XP 💪</span></p>
      </div>
      </div>
    </div>
  </aside>

  <!-- Challenge Stage (first on mobile) -->
  <div id="challenge-stage" class="study-card x-panel span-12 min-h-[280px] p-6 flex flex-col justify-center transition-all order-first lg:order-none" data-reveal data-tilt style="--d:.08s">
    <div class="text-center py-10">
      <div class="w-12 h-12 mx-auto" data-lottie-fly="trophy" aria-hidden="true"></div>
      <p class="text-sm text-slate-300 font-medium">Ready when you are.</p>
      <p class="text-xs text-slate-500 mt-1">Press "Commence Challenge" above to initiate the timer.</p>
    </div>
  </div>
</div>
</div>
