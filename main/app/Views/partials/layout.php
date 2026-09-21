<?php $imgUrl = rtrim(base_url(), '/'); ?>
<!DOCTYPE html>
<html lang="en" data-theme="gate-dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title) ?> · GATE 2027 Arena</title>
  <link rel="icon" href="data:,">
  <meta name="description" content="GATE 2027 gaming study arena — quests, XP, streaks, drills and analytics.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap">
  <?php $av = @filemtime(FCPATH . 'assets/app.css') ?: time(); $jv = @filemtime(FCPATH . 'assets/app.js') ?: time(); ?>
  <link rel="stylesheet" href="<?= $imgUrl ?>/assets/app.css?v=<?= $av ?>" fetchpriority="high">
  <link rel="preload" href="https://fonts.googleapis.com/css2?family=Sora:wght@700;800&display=swap" as="style">
  <?php
  $preloadMap = ['dashboard' => 'chunk-dashboard.js', 'course' => 'chunk-course.js', 'learn' => 'chunk-course.js', 'training' => 'chunk-training.js', 'challenge' => 'chunk-training.js', 'performance' => 'chunk-performance.js', 'documents' => 'chunk-documents.js', 'calendar' => 'chunk-calendar.js', 'settings' => 'chunk-settings.js', 'game' => 'chunk-game.js'];
  $preloadFile = $preloadMap[$page] ?? null;
  if ($preloadFile && is_file(FCPATH . 'assets/' . $preloadFile)):
  ?>
  <link rel="modulepreload" href="<?= $imgUrl ?>/assets/<?= $preloadFile ?>?v=<?= @filemtime(FCPATH . 'assets/' . $preloadFile) ?>">
  <?php endif; ?>
  <script>window.GATE_BASE = '<?= $imgUrl ?>';</script>
  <style>
    /* Boot preloader: inline for zero-JS first paint (quest arena loader) */
    #boot-preloader { position: fixed; inset: 0; z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; background: radial-gradient(900px 420px at 50% 0%, rgba(124,58,237,.22), transparent 65%), #0b1020; transition: opacity .45s ease, visibility .45s; }
    #boot-preloader.done { opacity: 0; visibility: hidden; pointer-events: none; }
    #boot-preloader .orb { display: grid; place-items: center; width: 4rem; height: 4rem; border-radius: 1.25rem; font-size: 2rem; background: linear-gradient(135deg,#7c3aed,#a855f7 55%,#ec4899); box-shadow: 0 0 40px -4px rgba(124,58,237,.8), inset 0 1px 0 rgba(255,255,255,.3); animation: bootPulse 1.6s ease-in-out infinite; }
    @keyframes bootPulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.08); } }
    #boot-preloader .ring { width: 11rem; height: 0.35rem; border-radius: 9999px; background: rgba(255,255,255,.08); overflow: hidden; }
    #boot-preloader .ring > span { display: block; height: 100%; width: 30%; border-radius: inherit; background: linear-gradient(90deg,#7c3aed,#22d3ee,#f59e0b); animation: bootSlide 1.1s ease-in-out infinite; }
    @keyframes bootSlide { 0% { margin-left: -30%; } 100% { margin-left: 100%; } }
    #boot-preloader .tip { font-size: .78rem; color: #94a3b8; }
    #boot-preloader .tip b { color: #c4b5fd; }
    @media (prefers-reduced-motion: reduce) { #boot-preloader .orb, #boot-preloader .ring > span { animation: none; } }
  </style>
  <noscript><style>#boot-preloader { display: none; }</style></noscript>
</head>
<body data-page="<?= esc($page) ?>" class="game-shell min-h-screen">
  <div id="boot-preloader" role="status" aria-label="Loading GATE Arena">
    <div class="orb">🎯</div>
    <p class="font-display text-sm font-bold text-white">GATE <span class="text-quest">2027</span> Arena</p>
    <div class="ring"><span></span></div>
    <p class="tip" id="boot-tip">Summoning <b>quests</b>…</p>
  </div>
  <script>
    (function () {
      var tips = ['Summoning <b>quests</b>…', 'Charging <b>XP</b>…', 'Aligning <b>constellation</b>…', 'Sharpening <b>PYQs</b>…'];
      var i = 0, tip = document.getElementById('boot-tip');
      var iv = setInterval(function () { i = (i + 1) % tips.length; if (tip) tip.innerHTML = tips[i]; }, 1600);
      var done = false;
      function hide() {
        if (done) return; done = true; clearInterval(iv);
        var p = document.getElementById('boot-preloader');
        if (p) { p.classList.add('done'); setTimeout(function () { p.remove(); }, 500); }
      }
      window.addEventListener('load', function () { setTimeout(hide, 350); });
      setTimeout(hide, 4000);
    })();
  </script>
  <div class="bg-grid pointer-events-none fixed inset-0" aria-hidden="true"></div>
  <div class="side-scrim" id="side-scrim"></div>

  <?php
  $groups = [
    'Learn' => [
      'dashboard' => ['/dashboard', 'Dashboard', 'layout-dashboard', 'G'],
      'course' => ['/course', 'Course', 'compass', 'C'],
    ],
    'Practice' => [
      'training' => ['/training', 'Training', 'target', 'T'],
      'challenge' => ['/challenge', 'Challenge', 'trophy', 'H'],
      'game' => ['/game', 'Arcade Game', 'gamepad-2', 'A'],
    ],
    'Track' => [
      'performance' => ['/performance', 'Performance', 'trending-up', 'P'],
      'calendar' => ['/calendar', 'Calendar', 'calendar-days', 'L'],
      'documents' => ['/documents', 'Documents', 'folder-kanban', 'D'],
    ],
    'System' => [
      'settings' => ['/settings', 'Settings', 'sliders', 'S'],
    ],
  ];
  $base = rtrim(base_url(), '/');
  ?>

  <aside class="game-sidebar" id="game-sidebar" aria-label="Game navigation">
    <a href="<?= $imgUrl ?>/" class="game-brand hov-grow">
      <span class="orb level-ring">🎯</span>
      <span class="side-txt"><b>GATE <span class="text-quest">2027</span></b><small>STUDY ARENA</small></span>
    </a>

    <div class="side-profile flex items-center gap-2.5 rounded-2xl border border-white/10 bg-black/25 px-3 py-2.5">
      <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-600 to-fuchsia-500 text-sm font-extrabold text-white">G</span>
      <div class="side-txt min-w-0">
        <p class="truncate text-xs font-bold text-slate-200">Welcome, Aspirant</p>
        <p class="font-mono text-[10px] text-slate-500">Lv <?= $level ?> · <?= $xpTotal ?> XP · 🔥<?= $streak ?></p>
      </div>
    </div>

    <div class="side-player" data-tip="Level up: 500 XP per level. Practice, log focus, clear quests.">
      <div class="lvl"><span class="text-amber-300">Lv <?= $level ?></span><span class="font-mono text-[11px] text-slate-400"><?= $xpTotal ?> XP</span></div>
      <div class="xp-track mt-2 h-2"><div class="xp-fill h-full rounded-full" data-w="<?= min(100, ($xpTotal % 500) / 5) ?>"></div></div>
      <div class="mt-2 flex flex-wrap gap-1.5">
        <span class="chip chip-streak"><span class="streak-flame">🔥</span><span class="side-txt"><?= $streak ?> day streak</span></span>
        <span class="chip chip-quest"><span class="side-txt"><?= $daysLeft ?>d left</span></span>
      </div>
    </div>

    <nav aria-label="Primary">
      <?php foreach ($groups as $gname => $items): ?>
      <p class="side-label"><?= $gname ?></p>
      <div class="side-nav">
        <?php foreach ($items as $key => [$path, $label, $icon, $kbd]):
          $full = $base . $path;
          $isActive = $active === $key;
        ?>
        <a href="<?= $full ?>" data-tip="<?= $label ?> quest hub" class="side-link <?= $isActive ? 'active' : '' ?>">
          <span data-lucide="<?= $icon ?>"></span>
          <span class="side-txt"><?= $label ?></span>
          <kbd class="kbd side-txt"><?= $kbd ?></kbd>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </nav>
  </aside>

  <div class="game-content">
    <div class="game-topbar">
      <button id="side-open" class="btn btn-ghost btn-sm rounded-xl lg:hidden" aria-label="Open navigation">☰</button>
      <div class="min-w-0 flex-1">
        <p class="truncate font-display text-sm font-bold"><?= esc($title) ?> <span class="text-quest">· Quest Hub</span></p>
      </div>
      <label class="top-search-pill hidden items-center gap-2 px-3 py-1.5 md:flex">
        <span data-lucide="search" class="h-3.5 w-3.5 text-slate-500"></span>
        <input id="top-search" type="search" placeholder="Search docs…  ( / )" class="w-36 bg-transparent text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none">
      </label>
      <div class="dropdown dropdown-end hidden sm:block">
        <button tabindex="0" class="btn btn-ghost btn-sm btn-circle relative" aria-label="Notifications, 3 unread alerts" data-tip="Due reviews & quest alerts">
          <span data-lucide="bell" class="h-4 w-4"></span>
          <span aria-hidden="true" class="absolute right-1 top-1 grid h-4 min-w-4 place-items-center rounded-full bg-emerald-500 px-1 font-mono text-[9px] font-bold text-white">3</span>
        </button>
        <ul tabindex="0" class="dropdown-content menu z-50 mt-2 w-64 rounded-2xl border border-white/10 bg-[#131a32] p-2 text-xs shadow-2xl">
          <li class="menu-title px-2 text-slate-500">Quest alerts</li>
          <li><a href="<?= $base ?>/training"><span>⚔</span> Due reviews waiting in Training</a></li>
          <li><a href="<?= $base ?>/calendar"><span>📅</span> Today's mission is scheduled</a></li>
          <li><a href="<?= $base ?>/performance"><span>📈</span> Weekly accuracy report ready</a></li>
        </ul>
      </div>
      <span class="chip chip-xp xp-shine hidden sm:inline-flex"><span>Lv <?= $level ?> · <?= $xpTotal ?> XP</span></span>
      <a href="<?= $base ?>/training" class="btn btn-sm btn-quest btn-press rounded-xl hov-glow">⚔ Drill XP</a>
    </div>

    <div class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-10 pb-24 lg:pb-10">
      <main data-reveal>
        <?= $content ?>
      </main>
      <footer class="mt-6 rounded-2xl border border-white/5 bg-white/[0.02] px-4 py-4 text-center text-xs text-slate-500">
        <p>⚔️ One quest a day keeps the backlog away · <kbd>/</kbd> search docs · <kbd>1–4</kbd> answer · <kbd>Enter</kbd> next · <kbd>G C T P L D S</kbd> jump</p>
        <p class="mt-1 text-[10px] text-slate-600">Admin theme design by <a href="https://colorlib.com" target="_blank" rel="noopener" class="underline hover:text-slate-400">Colorlib</a> (Gentelella, MIT) — ported as dark Tailwind tokens.</p>
      </footer>
    </div>
  </div>

  <nav class="quest-tabbar fixed bottom-0 left-0 right-0 z-40 grid grid-cols-8 px-1 pt-1 lg:hidden" aria-label="Quick" style="padding-bottom: env(safe-area-inset-bottom);">
    <?php
    $tabs = ['dashboard' => ['/dashboard','Home','layout-dashboard'],'course' => ['/course','Course','compass'],'training' => ['/training','Train','target'],'challenge' => ['/challenge','Duel','trophy'],'performance' => ['/performance','Stats','trending-up'],'calendar' => ['/calendar','Plan','calendar-days'],'documents' => ['/documents','Docs','folder-kanban'],'settings' => ['/settings','Setup','sliders']];
    foreach ($tabs as $key => [$path, $label, $icon]) :
        $full = $base . $path;
    ?>
    <a href="<?= $full ?>" class="quest-tab hov-lift <?= $active === $key ? 'quest-tab-active' : '' ?>">
      <span data-lucide="<?= $icon ?>" class="h-5 w-5"></span><span><?= $label ?></span>
    </a>
    <?php endforeach; ?>
  </nav>

  <!-- Static AI launcher: full chat (~300KB KaTeX/highlight) loads only on first click -->
  <button id="ai-launcher-static" class="anim-pulse-glow fixed bottom-5 right-5 z-[90] grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-violet-600 via-purple-500 to-fuchsia-500 text-white shadow-2xl" aria-label="Ask GATE AI" data-tip="Ask GATE AI anything">
    <span data-lucide="sparkles" class="h-6 w-6"></span>
  </button>

  <script src="<?= $imgUrl ?>/assets/app.js?v=<?= $jv ?>" type="module"></script>
</body>
</html>
