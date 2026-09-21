<?php
$minutesToday = (int) ($minutesToday ?? 0);
$totalTopics = array_sum(array_column($subjects ?? [], 'total'));
$doneTopics = array_sum(array_column($subjects ?? [], 'done'));
$quizzesTaken = (int) ($mcqCount ?? 0);
$h = intdiv($minutesToday, 60); $m = $minutesToday % 60;
$studiedLabel = sprintf('%02d:%02d', $h, $m);

function masteryLevel(int $pct): array {
    return match (true) {
        $pct >= 95 => ['Legend', '#f59e0b', 'lvl-gold'],
        $pct >= 80 => ['Master', '#10b981', 'lvl-green'],
        $pct >= 60 => ['Scholar', '#38bdf8', 'lvl-white'],
        $pct >= 40 => ['Rising', '#818cf8', 'lvl-blue'],
        $pct >= 20 => ['Rookie', '#f43f5e', 'lvl-red'],
        default => ['Beginner', '#94a3b8', 'lvl-black'],
    };
}
?>
<div class="space-y-6 sm:space-y-8">

  <!-- 1 · Daily Quest Hero -->
  <section class="quest-hero p-6 sm:p-7 rounded-2xl border border-white/10 shadow-2xl relative overflow-hidden" data-reveal>
    <div id="particles-hero" data-particles aria-hidden="true"></div>
    <div class="grid gap-5 lg:grid-cols-12 relative z-10">
      <div class="min-w-0 space-y-3 lg:col-span-8" data-hero-item>
        <div class="flex flex-wrap items-center gap-2">
          <span class="chip chip-quest text-xs font-bold"><span data-lucide="compass" class="h-3.5 w-3.5"></span><span>Daily Quest · <?= esc(date('D, d-m-Y')) ?></span></span>
          <?php if ($mission && $mission['status'] === 'done'): ?>
            <span class="chip chip-focus text-xs font-bold">✔ Quest Completed</span>
          <?php else: ?>
            <span class="chip chip-xp xp-shine text-xs font-bold">+20 XP reward</span>
          <?php endif; ?>
        </div>
        <?php if ($mission): ?>
          <h1 class="text-2xl font-extrabold text-white sm:text-3xl tracking-tight leading-snug" data-split>
            <?= esc($mission['subject'] ? '[' . $mission['subject'] . '] ' : '') ?><?= esc($mission['task']) ?>
          </h1>
          <svg class="mt-1 h-4 w-56 sm:w-72" viewBox="0 0 288 16" fill="none" aria-hidden="true">
            <path id="hero-underline" d="M4 12 C 60 4, 120 4, 168 9 S 260 13, 284 7" stroke="url(#heroGrad)" stroke-width="3" stroke-linecap="round" data-svg-draw />
            <defs><linearGradient id="heroGrad" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#a855f7"/><stop offset=".5" stop-color="#22d3ee"/><stop offset="1" stop-color="#f59e0b"/></linearGradient></defs>
          </svg>
          <span class="pointer-events-none absolute left-0 top-0 z-10 text-lg" data-motion-path="#hero-underline" aria-hidden="true">🚀</span>
          <p class="flex flex-wrap items-center gap-2 text-sm text-slate-300">
            <?php if ($mission['pyqs']): ?>
              <span class="chip chip-xp font-mono"><span data-lucide="target" class="h-3 w-3"></span> +<?= (int) $mission['pyqs'] ?> target PYQs</span>
            <?php endif; ?>
            <span class="text-slate-400"><?= $mission['status'] !== 'done' ? '1 quest · 1 timed drill · 25 min revision.' : 'Quest cleared! Take a practice drill to level up faster.' ?></span>
          </p>
        <?php else: ?>
          <h1 class="text-2xl font-extrabold text-white sm:text-3xl">No quest scheduled today.</h1>
          <p class="text-sm text-slate-400">Click Sync Notion in Settings or Calendar to refresh your day-wise plan.</p>
        <?php endif; ?>
        <div class="flex flex-wrap items-center gap-2.5 pt-2">
          <?php if ($mission): ?>
          <button id="btn-done-mission" data-date="<?= esc($mission['task_date']) ?>"
            class="btn btn-sm btn-press <?= $mission['status'] === 'done' ? 'btn-success' : 'btn-primary bg-emerald-600 hover:bg-emerald-500 border-emerald-500 text-white shadow-lg shadow-emerald-950/40' ?> gap-2 rounded-xl px-5 font-bold hov-glow">
            <span data-lucide="check" class="h-4 w-4"></span>
            <span><?= $mission['status'] === 'done' ? 'Done ✓' : 'Complete Quest (+20 XP)' ?></span>
          </button>
          <?php endif; ?>
          <a href="<?= !empty($mission['notion_url']) ? esc($mission['notion_url']) : 'https://app.notion.com/p/7eaffc35b21b49d8942da238d562359a' ?>" target="_blank"
             class="btn btn-sm rounded-xl border border-amber-400/30 bg-amber-500/10 gap-1.5 text-amber-300 hover:bg-amber-500/20 font-semibold hov-lift">
            <span data-lucide="external-link" class="h-3.5 w-3.5"></span><span>Notion Workspace</span>
          </a>
        </div>
        <p class="font-mono text-[11px] text-violet-300 pt-1" data-typed="Small steps daily beat last-month panic.||One quest a day keeps the backlog away.||Focus 25 minutes. Then drill. Then rest."></p>
      </div>

      <div class="grid content-start gap-4 lg:col-span-4" data-hero-item>
        <div class="rounded-xl border border-white/10 bg-black/30 p-4" data-tilt>
          <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Streak Shield</p>
          <p class="mt-1 text-base font-extrabold text-white flex items-center gap-1.5"><span class="streak-flame">🔥</span> <?= (int) $streak ?> days</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-black/30 p-4" data-tilt>
          <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Level Progress</p>
          <div class="xp-track mt-2 h-2"><div class="xp-fill h-full rounded-full" data-w="<?= min(100, ($xpTotal % 500) / 5) ?>"></div></div>
          <p class="mt-1 font-mono text-[11px] text-amber-300">Lv <?= $level ?> · <?= $xpTotal ?> XP</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 2 · Stat strip (Gentelella tile_count) -->
  <section class="study-card w-full p-4 sm:p-5 rounded-2xl" data-reveal>
  <div class="stat-quad">
    <div class="tile-stats flex flex-col p-5 rounded-xl border border-white/10 bg-slate-900/60 hov-lift min-h-[172px] lg:min-h-[190px] border-t-[3px]" style="border-top-color:#1abb9c" data-tilt>
      <span class="t-icon" aria-hidden="true"><span data-lucide="clock"></span></span>
      <p class="my-2 flex items-baseline gap-1.5 text-2xl font-extrabold text-white leading-none"><span id="stat-minutes" data-count="<?= $minutesToday ?>"><?= $minutesToday ?></span><span class="text-xs font-normal text-slate-400 whitespace-nowrap">min</span></p>
      <h2>Studied Today</h2>
      <p id="studied-today-display" class="hidden"><?= $studiedLabel ?></p>
      <span class="t-foot"><?= $minutesToday > 0 ? $studiedLabel . ' logged today' : 'No focus yet — launch timer' ?></span>
      <button id="btn-timer" class="btn btn-sm btn-press w-full gap-1 rounded-xl bg-emerald-600 text-white hover:bg-emerald-500 font-semibold" style="margin-top:auto">
        <span data-lucide="timer" class="h-3 w-3"></span><span>Launch Focus Timer</span>
      </button>
    </div>

    <div class="tile-stats flex flex-col p-5 rounded-xl border border-white/10 bg-slate-900/60 hov-lift min-h-[172px] lg:min-h-[190px] border-t-[3px]" style="border-top-color:#8b5cf6" data-tilt>
      <span class="t-icon" aria-hidden="true"><span data-lucide="award"></span></span>
      <p class="my-2 flex items-baseline gap-1.5 text-2xl font-extrabold text-white leading-none"><span data-count="<?= $doneTopics ?>"><?= $doneTopics ?></span><span class="text-xs font-normal text-slate-400 whitespace-nowrap">/ <?= $totalTopics ?></span></p>
      <h2>Topics Done</h2>
      <span class="t-foot"><?= ($doneTopics === 0 && $totalTopics > 0) ? 'Mark topics done in Course' : round($totalTopics ? $doneTopics / $totalTopics * 100 : 0) . '% of syllabus complete' ?></span>
      <a href="<?= esc($baseUrl) ?>/course" class="dash-box-footer" style="margin-top:auto">View Roadmap →</a>
    </div>

    <div class="tile-stats flex flex-col p-5 rounded-xl border border-white/10 bg-slate-900/60 hov-lift min-h-[172px] lg:min-h-[190px] border-t-[3px]" style="border-top-color:#f59e0b" data-tilt>
      <span class="t-icon" aria-hidden="true"><span data-lucide="help-circle"></span></span>
      <p class="my-2 flex items-baseline gap-1.5 text-2xl font-extrabold text-white leading-none"><span data-count="<?= $quizzesTaken ?>"><?= $quizzesTaken ?></span><span class="text-xs font-normal text-slate-400 whitespace-nowrap">questions</span></p>
      <h2>MCQ Bank</h2>
      <span class="t-foot">Ready to practice</span>
      <a href="<?= esc($baseUrl) ?>/training" class="dash-box-footer" style="margin-top:auto">Practice Arena →</a>
    </div>

    <div class="tile-stats flex flex-col p-5 rounded-xl border border-white/10 bg-slate-900/60 hov-lift min-h-[172px] lg:min-h-[190px] border-t-[3px]" style="border-top-color:#3498db" data-tilt>
      <span class="t-icon" aria-hidden="true"><span data-lucide="calendar"></span></span>
      <p class="my-2 flex items-baseline gap-1.5 text-2xl font-extrabold text-white leading-none"><span data-count="<?= (int) ($daysToExam ?? 0) ?>"><?= (int) ($daysToExam ?? 0) ?></span><span class="text-xs font-normal text-slate-400 whitespace-nowrap">days left</span></p>
      <h2>Exam Countdown</h2>
      <span class="t-foot">GATE Feb 2027 · Phase 1</span>
      <a href="<?= esc($baseUrl) ?>/calendar" class="dash-box-footer" style="margin-top:auto">View Schedule →</a>
    </div>
  </div>
  </section>

  <!-- 3 · Subject Mastery Grid -->
  <section class="study-card x-panel p-6 rounded-2xl shadow-xl" data-reveal style="--d:.1s">
    <div class="x-title">
      <div class="flex items-center gap-2.5">
        <span class="p-2 rounded-xl bg-indigo-500/15 text-indigo-400 border border-indigo-500/30">
          <span data-lucide="compass" class="h-4 w-4"></span>
        </span>
        <div>
          <h2 class="text-base font-extrabold text-white">Subject Mastery Radar</h2>
          <p class="text-xs text-slate-400">Track topic completion & launch subject drill sessions</p>
        </div>
      </div>
      <span class="chip chip-quest text-xs font-bold"><?= count($subjects) ?> Subjects in Syllabus</span>
      <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
    </div>
    <div class="x-content">

    <div id="subject-grid" class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <?php foreach ($subjects as $s):
          $tot = (int) ($s['total'] ?? 0);
          $dn = (int) ($s['done'] ?? 0);
          $pct = $tot ? round($dn / $tot * 100) : 0;
          [$lvlName, $lvlColor, $lvlCls] = masteryLevel($pct);
          $subjColor = $s['color'] ?: '#6366f1';
      ?>
      <div class="rounded-xl border border-white/10 bg-slate-900/80 p-4 hover:border-indigo-500/50 hover:bg-slate-800/90 transition-all flex flex-col justify-between shadow-md group hov-lift" data-subject-id="<?= (int) $s['id'] ?>">
        <div>
          <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 min-w-0">
              <span class="w-8 h-8 rounded-lg grid place-items-center text-xs font-extrabold shrink-0" style="background: <?= esc($subjColor) ?>22; border: 1px solid <?= esc($subjColor) ?>55; color: <?= esc($subjColor) ?>">
                <?= esc(substr($s['code'] ?: $s['name'], 0, 3)) ?>
              </span>
              <p class="truncate text-sm font-bold text-slate-100 group-hover:text-white transition-colors" title="<?= esc($s['name']) ?>"><?= esc($s['name']) ?></p>
            </div>
            <span class="text-[11px] font-mono font-bold px-2 py-0.5 rounded-full" style="background: <?= esc($lvlColor) ?>22; color: <?= esc($lvlColor) ?>; border: 1px solid <?= esc($lvlColor) ?>44">
              <?= $pct ?>%
            </span>
          </div>

          <div class="mt-3.5">
            <div class="flex items-center justify-between text-[11px] text-slate-400">
              <span><b class="text-slate-200"><?= $dn ?></b> of <?= $tot ?> topics</span>
              <span class="font-semibold" style="color: <?= esc($lvlColor) ?>"><?= $lvlName ?></span>
            </div>
            <div class="bar-track mt-1.5 h-1.5 w-full bg-slate-950 rounded-full overflow-hidden">
              <div class="bar-fill h-full rounded-full transition-all duration-700" data-w="<?= $pct ?>" style="background: <?= esc($lvlColor) ?>; width: <?= $pct ?>%"></div>
            </div>
          </div>
        </div>

        <a href="<?= esc($baseUrl) ?>/training?subject=<?= (int) $s['id'] ?>" class="mt-4 btn btn-xs w-full rounded-lg border border-white/10 bg-slate-800/70 hover:bg-indigo-600 hover:text-white hover:border-indigo-500 text-slate-300 font-bold transition-all flex items-center justify-center gap-1.5 py-2">
          <span>⚔ Practice Drills</span>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    </div>
  </section>

  <!-- 4 · Accuracy (primary) -->
  <div class="grid gap-6">
    <section class="study-card x-panel p-5 rounded-2xl" data-reveal data-tilt style="--d:.12s">
      <div class="x-title">
        <div class="flex items-center gap-2">
          <span data-lucide="trending-up" class="h-4 w-4 text-violet-300"></span>
          <h2 class="text-sm font-bold text-white">Recent Accuracy Trend</h2>
        </div>
        <a href="<?= esc($baseUrl) ?>/performance" class="text-[11px] font-semibold text-violet-300 hover:text-violet-200">Full Analytics →</a>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content"><div id="accuracy-chart" class="chart-loading mt-4 h-56"></div></div>
    </section>
  </div>

  <!-- 5 · Secondary grid: 6 auto-wrapping cards (heatmap / prep / links / orbit) -->
  <div class="dash-secondary" data-reveal>
    <section class="study-card x-panel span-2 p-5 rounded-2xl" data-tilt style="--d:.14s">
      <div class="x-title">
        <div class="flex items-center gap-2">
          <span data-lucide="calendar-clock" class="h-4 w-4 text-emerald-400"></span>
          <h2 class="text-sm font-bold text-white">Consistency Heatmap</h2>
        </div>
        <div class="flex items-center gap-1 font-mono text-[10px] text-slate-500">
          <span>Less</span>
          <span class="h-2.5 w-2.5 rounded" style="background:#1a2340"></span>
          <span class="h-2.5 w-2.5 rounded" style="background:#4c1d95"></span>
          <span class="h-2.5 w-2.5 rounded" style="background:#7c3aed"></span>
          <span class="h-2.5 w-2.5 rounded" style="background:#f59e0b"></span>
          <span>More</span>
        </div>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content">
      <div id="heatmap" class="mt-4 flex justify-center overflow-x-auto py-2"></div>
      <div id="study-heatmap" class="hidden"></div>
      </div>
    </section>
    <div class="study-card flex flex-col p-5 rounded-2xl hov-lift min-h-[148px] border-t-[3px]" style="border-top-color:#6366f1" data-tilt>
      <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
          <span class="rounded-lg bg-indigo-500/15 p-1.5 text-indigo-300"><span data-lucide="calendar" class="h-4 w-4"></span></span>
          <span class="text-xs font-semibold text-slate-400">Prep Starts</span>
        </div>
        <span class="text-xs font-mono font-bold shrink-0 <?= ($daysToStart ?? 0) > 0 ? 'text-amber-300 bg-amber-400/15 border border-amber-400/30 px-2.5 py-0.5 rounded-full' : 'text-emerald-400 bg-emerald-500/15 border border-emerald-500/30 px-2.5 py-0.5 rounded-full' ?>">
          <?= ($daysToStart ?? 0) > 0 ? esc($daysToStart) . 'd to start' : 'Phase 1 Active' ?>
        </span>
      </div>
      <p class="mt-3 text-base font-extrabold text-white"><?= esc(date('D, d-m-Y', strtotime($startDate ?? '2026-09-21'))) ?></p>
      <p class="text-[11px] text-slate-500" style="margin-top:auto">Phase 1 · Discrete Maths + C · 18h/week</p>
    </div>

      <a href="https://gate2027.iisc.ac.in" target="_blank" class="study-card flex flex-col p-5 rounded-2xl hov-lift min-h-[148px] border-t-[3px] block" style="border-top-color:#fb4d6d;overflow-wrap:anywhere" data-tilt>
        <div class="flex items-center justify-between gap-2">
          <span class="rounded-lg bg-rose-500/15 p-1.5 text-rose-300"><span data-lucide="bell-ring" class="h-4 w-4"></span></span>
          <span class="text-[10px] font-mono font-bold text-rose-300"><?= ($daysToReg ?? 12) ?>d left</span>
        </div>
        <p class="mt-3 text-sm font-extrabold text-white">Registration</p>
        <p class="text-[11px] text-slate-500" style="margin-top:auto">GATE 2027 on GOAPS ↗</p>
      </a>
      <a href="<?= esc($baseUrl) ?>/course" class="study-card flex flex-col p-5 rounded-2xl hov-lift min-h-[148px] border-t-[3px] block" style="border-top-color:#8b5cf6" data-tilt>
        <div class="flex items-center justify-between gap-2">
          <span class="rounded-lg bg-indigo-500/15 p-1.5 text-indigo-300"><span data-lucide="compass" class="h-4 w-4"></span></span>
          <span class="text-[10px] font-mono font-bold text-indigo-300">Daily</span>
        </div>
        <p class="mt-3 text-sm font-extrabold text-white">Daily Routine</p>
        <p class="text-[11px] text-slate-500" style="margin-top:auto">18h/week · View Topics →</p>
      </a>
      <a href="<?= esc($baseUrl) ?>/challenge" class="study-card flex flex-col p-5 rounded-2xl hov-lift min-h-[148px] border-t-[3px] block" style="border-top-color:#f59e0b" data-tilt>
        <div class="flex items-center justify-between gap-2">
          <span class="rounded-lg bg-amber-500/15 p-1.5 text-amber-300"><span data-lucide="trophy" class="h-4 w-4"></span></span>
          <span class="text-[10px] font-mono font-bold text-amber-300">Sundays</span>
        </div>
        <p class="mt-3 text-sm font-extrabold text-white">Sunday Mock</p>
        <p class="text-[11px] text-slate-500" style="margin-top:auto">6h Mega Mock →</p>
      </a>

    <!-- 6 · Mastery Orbit (professional planetarium, spans 2) -->
    <section class="study-card x-panel span-2 p-5 rounded-2xl overflow-hidden" data-tilt style="--d:.18s">
      <div class="x-title">
        <div class="flex items-center gap-2">
          <span class="rounded-lg bg-violet-500/15 p-1.5 text-violet-300"><span data-lucide="compass" class="h-4 w-4"></span></span>
          <h2 class="text-sm font-bold text-white">Mastery Orbit</h2>
          <span class="chip chip-quest hidden sm:inline-flex text-[10px]">Live · <?= count($subjects) ?> subjects</span>
        </div>
        <p id="viz3d-info" class="font-mono text-[11px] text-slate-400">Hover a planet · Click to drill</p>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content">
      <div id="viz3d" role="img" aria-label="Subject mastery orbit" class="mt-3 h-56 sm:h-64 w-full min-h-[220px] overflow-hidden rounded-xl border border-white/5" style="background:radial-gradient(ellipse 80% 90% at 50% 110%, rgba(124,58,237,.14), transparent 60%), #0a0f22"></div>
      <p class="mt-2 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 px-3 pb-2 pt-2 text-center text-xs text-slate-500">
        <span>Size &amp; color = mastery.</span>
        <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full" style="background:#10b981"></span>≥70 ready</span>
        <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full" style="background:#f59e0b"></span>50–69</span>
        <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full" style="background:#f43f5e"></span>&lt;50</span>
      </p>
      </div>
    </section>
  </div>
</div>

<!-- Focus timer dialog -->
<dialog id="study-timer-modal" class="modal modal-middle bg-black/70 backdrop-blur-md">
  <div class="modal-box max-w-sm rounded-2xl border border-white/10 bg-[#131a32] p-6 text-center shadow-2xl">
    <div class="flex items-center justify-between border-b divider-soft pb-4">
      <h3 class="flex items-center gap-2 font-bold text-white">
        <span class="anim-breath grid h-8 w-8 place-items-center rounded-xl bg-emerald-500/15 text-emerald-400">⏱</span>
        <span>Focus Session</span>
      </h3>
      <form method="dialog"><button class="btn btn-xs btn-ghost rounded-lg" aria-label="Close">✕</button></form>
    </div>
    <div class="my-6">
      <div id="timer-display" class="font-mono text-5xl font-extrabold tracking-widest text-white">00:00</div>
      <div id="timer-digits" class="hidden">00:00</div>
      <p class="mt-2 text-xs text-slate-400">Deep focus now, XP when you log. Every minute shields your streak.</p>
      <div class="chip chip-focus mx-auto mt-3">📚 distraction-free · breathe · solve</div>
    </div>
    <div class="flex gap-2">
      <button id="timer-start" class="btn btn-sm flex-1 rounded-xl bg-emerald-600 text-white hover:bg-emerald-500">Start Focus</button>
      <button id="timer-stop" class="btn btn-sm flex-1 rounded-xl" disabled>Stop &amp; Claim XP</button>
    </div>
  </div>
</dialog>
