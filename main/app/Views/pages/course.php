<?php
$topicsByPhase = [];
foreach ($topics as $t) {
    $topicsByPhase[(int) $t['phase_id']][] = $t;
}
function topicStatusBadge(string $s): array {
    return match ($s) {
        'done' => ['check-circle-2', 'chip chip-focus text-[11px]', 'Completed'],
        'active' => ['play-circle', 'chip chip-xp text-[11px]', 'In Progress'],
        default => ['lock', 'chip text-slate-400 border-slate-700/80 bg-slate-800/40 text-[11px]', 'Start'],
    };
}
?>
<div class="space-y-6">
  <div data-reveal class="quest-hero p-6 flex flex-wrap items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
        <span class="p-2 rounded-xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400">
          <span data-lucide="compass" class="w-6 h-6"></span>
        </span>
        <span>Course Mode — <span class="text-quest">GATE 2027 Roadmap</span></span>
      </h1>
      <p class="mt-1 text-sm text-slate-400" data-quote>Learn concepts → Master formulas → Drill interactive practice → Track retention.</p>
    </div>
    <div class="flex items-center gap-2">
      <span class="chip chip-quest text-xs">
        <span data-lucide="award" class="w-3.5 h-3.5"></span> <?= count($topics) ?> Total Topics
      </span>
      <span class="chip chip-focus text-xs">
        <span data-lucide="check-circle-2" class="w-3.5 h-3.5"></span> <?= count(array_filter($topics, fn ($t) => $t['status'] === 'done')) ?> Mastered
      </span>
    </div>
  </div>

  <?php foreach ($phases as $i => $phase):
      $items = $topicsByPhase[(int) $phase['id']] ?? [];
      $total = count($items);
      $done = count(array_filter($items, fn ($t) => $t['status'] === 'done'));
      $pct = $total ? round($done / $total * 100) : 0;
      $phaseNo = $i + 1;
      // Chip follows the number inside the phase name when present
      // (loop position lies when legacy/duplicate phases exist).
      $chipNo = $phaseNo;
      if (preg_match('/Phase\s*(\d+)/i', (string) ($phase['name'] ?? ''), $mm)) {
          $chipNo = (int) $mm[1];
      }
      $span = $i % 2 === 0 ? 'span-7' : 'span-5';
      if ($i % 2 === 0): ?>
  <div class="mosaic">
  <?php endif; ?>
  <section class="study-card x-panel <?= $span ?> p-6 relative overflow-hidden hov-lift" data-reveal data-tilt style="--d:<?= min($i, 5) * 70 ?>ms">
    <div class="x-title flex-wrap">
      <div>
        <div class="flex items-center gap-2">
          <span class="chip chip-quest uppercase tracking-wider text-[11px] font-bold">Phase <?= sprintf('%02d', $chipNo) ?></span>
          <?php if ($pct === 100): ?>
            <span class="chip chip-focus text-[11px]">Phase Mastered 🏆</span>
          <?php endif; ?>
        </div>
        <h2 class="mt-2 text-xl font-bold text-white"><?= esc($phase['name']) ?></h2>
        <?php if ($phase['start_date']): ?>
        <p class="mt-1 text-xs text-slate-400 flex items-center gap-1.5">
          <span data-lucide="calendar-days" class="w-3.5 h-3.5 text-slate-500"></span>
          <?= esc(date('d-m-Y', strtotime($phase['start_date']))) ?> → <?= esc(date('d-m-Y', strtotime($phase['end_date'] ?: $phase['start_date']))) ?>
        </p>
        <?php endif; ?>
      </div>
      <div class="flex items-center gap-4">
        <div class="text-right hidden sm:block">
          <p class="text-xs text-slate-400 font-mono"><?= $done ?> / <?= $total ?> topics</p>
          <div class="mt-1.5 h-2 w-44 rounded-full bg-slate-800/80 overflow-hidden">
            <div class="bar-fill h-2 rounded-full bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500" data-w="<?= $pct ?>"></div>
          </div>
        </div>
        <span class="text-xl font-extrabold text-indigo-300 tabular-nums"><?= $pct ?>%</span>
      </div>
      <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
    </div>
    <div class="x-content">

    <?php if ($items): ?>
    <div class="mt-5 grid gap-5">
      <?php foreach ($items as $t):
          [$iconName, $badgeClasses, $statusText] = topicStatusBadge($t['status']);
          // Topic names often already embed the subject ("DBMS: ...") —
          // strip a leading "{subject}:" so the card doesn't read "DBMS DBMS:".
          $fullName = (string) $t['name'];
          $displayName = $fullName;
          $subjName = trim((string) ($t['subject_name'] ?? ''));
          if ($subjName !== '') {
              for ($k = 0; $k < 2; $k++) {
                  $stripped = preg_replace('/^' . preg_quote($subjName, '/') . '\s*:\s*/i', '', $displayName);
                  if ($stripped === $displayName) break;
                  $displayName = trim((string) $stripped);
              }
          }
      ?>
      <a href="<?= esc($baseUrl) ?>/course/<?= (int) $t['id'] ?>"
         class="group flex items-center gap-3.5 rounded-xl border border-white/5 bg-slate-900/60 px-4 py-3.5 transition-all hover:border-indigo-500/40 hover:bg-slate-800/80 hover:shadow-lg">
        <span class="<?= $t['status'] === 'done' ? 'text-emerald-400' : ($t['status'] === 'active' ? 'text-amber-400' : 'text-slate-500') ?>">
          <span data-lucide="<?= $iconName ?>" class="w-5 h-5"></span>
        </span>
        <div class="min-w-0 flex-1">
          <p class="line-clamp-2 break-words text-sm font-semibold text-slate-200 group-hover:text-white transition-colors" title="<?= esc($fullName) ?>"><?= esc($displayName) ?></p>
          <?php if ($t['subject_name']): ?>
          <p class="text-xs mt-0.5 flex items-center gap-1.5 text-slate-400">
            <span class="inline-block w-1.5 h-1.5 rounded-full" style="background-color: <?= esc($t['color'] ?: '#6366f1') ?>"></span>
            <span><?= esc($t['subject_name']) ?></span>
          </p>
          <?php endif; ?>
        </div>
        <span class="<?= $badgeClasses ?>"><?= $statusText ?></span>
        <span data-lucide="chevron-right" class="w-4 h-4 text-slate-600 transition group-hover:translate-x-1 group-hover:text-indigo-300"></span>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
      <p class="mt-4 text-sm text-slate-500 py-4 text-center">No topics synced for this phase yet.</p>
    <?php endif; ?>
    </div>
  </section>
  <?php if ($i % 2 === 1 || $i === count($phases) - 1): ?>
  </div>
  <?php endif; ?>
  <?php endforeach; ?>

  <?php if (count($phases) === 0): ?>
      <div class="study-card p-12 text-center hov-lift">
    <span data-lucide="compass" class="w-8 h-8 mx-auto text-slate-500 mb-2"></span>
    <p class="text-slate-300 font-medium">Roadmap is empty.</p>
    <p class="text-xs text-slate-500 mt-1">Run a Notion sync from Settings to populate your phases and syllabus topics.</p>
  </div>
  <?php endif; ?>
</div>