<?php $base = rtrim(base_url(), '/'); ?>
<div class="grid gap-6 lg:grid-cols-[280px_1fr]">
  <!-- Mode picker -->
  <aside class="space-y-4" data-reveal>
    <div class="quest-hero p-5" data-tilt>
      <div id="particles-hero" data-particles aria-hidden="true"></div>
      <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2.5">
        <span class="p-1.5 rounded-xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 shrink-0">
          <span data-lucide="crosshair" class="w-5 h-5"></span>
        </span>
        <span data-split class="inline-block">Training <span class="text-quest">Arena</span></span>
      </h1>
      <p class="mt-2 text-xs text-slate-400 flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span><?= $mcqCount ?> active MCQs ready in bank</span>
      </p>
      <p class="mt-2 font-mono text-[11px] leading-relaxed text-violet-300" data-quote>Wrong answers today prevent wrong answers in February.</p>
      <model-viewer data-glb src="<?= $base ?>/models/hero.glb" poster="<?= $base ?>/media/poster.svg" auto-rotate camera-controls shadow-intensity="1" style="width:100%;height:180px" aria-hidden="true"></model-viewer>
    </div>

    <!-- Practice Modes Selection -->
    <div class="study-card p-4 space-y-5">
      <div class="text-[11px] font-mono uppercase tracking-wider text-slate-400 px-1 mb-2 font-bold">Select Workout Mode</div>
      <div class="grid gap-2.5 sm:grid-cols-2 lg:grid-cols-1" data-carousel>

      <button class="mode-btn group w-full flex items-center justify-between p-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-lg shadow-indigo-950/50 transition-all cursor-pointer border border-indigo-400/30" data-mode="quick" data-tip="10 questions · no timer">
        <span class="inline-flex items-center gap-2.5">
          <span data-lucide="zap" class="w-4 h-4 text-amber-300"></span>
          <span>Quick Drill</span>
        </span>
        <span class="text-[11px] font-mono px-2 py-0.5 rounded-md bg-white/20 text-white font-bold">10 Q</span>
      </button>

      <button class="mode-btn group w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-white/10 hover:border-indigo-400/40 text-slate-200 hover:text-white font-medium text-sm transition-all cursor-pointer" data-mode="timed" data-tip="15 questions · 2 minutes">
        <span class="inline-flex items-center gap-2.5">
          <span data-lucide="timer" class="w-4 h-4 text-cyan-400"></span>
          <span>Timed Sprint</span>
        </span>
        <span class="text-[11px] font-mono px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 border border-slate-700">2m</span>
      </button>

      <button class="mode-btn group w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-white/10 hover:border-indigo-400/40 text-slate-200 hover:text-white font-medium text-sm transition-all cursor-pointer" data-mode="random10" data-tip="10 random questions · 5 minutes">
        <span class="inline-flex items-center gap-2.5">
          <span data-lucide="dices" class="w-4 h-4 text-purple-400"></span>
          <span>Random 10</span>
        </span>
        <span class="text-[11px] font-mono px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 border border-slate-700">5m</span>
      </button>

      <button class="mode-btn group w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-white/10 hover:border-indigo-400/40 text-slate-200 hover:text-white font-medium text-sm transition-all cursor-pointer" data-mode="random25" data-tip="25 random questions · 10 minutes">
        <span class="inline-flex items-center gap-2.5">
          <span data-lucide="dices" class="w-4 h-4 text-pink-400"></span>
          <span>Random 25</span>
        </span>
        <span class="text-[11px] font-mono px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 border border-slate-700">10m</span>
      </button>

      <button class="mode-btn group w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-white/10 hover:border-emerald-400/40 text-slate-200 hover:text-white font-medium text-sm transition-all cursor-pointer" data-mode="fullmock" data-tip="Full GATE mock · 65 questions · 30 minutes">
        <span class="inline-flex items-center gap-2.5">
          <span data-lucide="flag" class="w-4 h-4 text-emerald-400"></span>
          <span>Full Mock GATE</span>
        </span>
        <span class="text-[11px] font-mono px-2 py-0.5 rounded-md bg-slate-800 text-emerald-400 border border-emerald-500/30 font-bold">65 Q</span>
      </button>

      <div class="pt-2 sm:col-span-2 lg:col-span-1">
        <a href="<?= $base ?>/challenge" class="group w-full flex items-center justify-between p-3 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-300 hover:text-amber-200 font-semibold text-sm transition-all">
          <span class="inline-flex items-center gap-2.5">
            <span data-lucide="trophy" class="w-4 h-4 text-amber-400"></span>
            <span>Daily Challenge</span>
          </span>
          <span class="chip chip-xp text-[10px]">+100 XP</span>
        </a>
      </div>
      </div>
    </div>

    <!-- Subject Filter -->
    <div class="study-card p-4" data-reveal style="--d:.08s">
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-300 mb-2">
        <span data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-indigo-400"></span>
        <span>Filter by Subject</span>
      </div>
      <select id="quiz-subject" class="select select-sm w-full bg-slate-800/80 border border-white/10 text-slate-200 rounded-xl focus:border-indigo-400">
        <option value="">All subjects (Mixed Bank)</option>
        <?php foreach ($subjects as $s): ?>
        <option value="<?= (int) $s['id'] ?>"><?= esc($s['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-300 mb-2 mt-3">
        <span data-lucide="flag" class="w-3.5 h-3.5 text-emerald-400"></span>
        <span>Filter by Phase</span>
      </div>
      <select id="quiz-phase" class="select select-sm w-full bg-slate-800/80 border border-white/10 text-slate-200 rounded-xl focus:border-emerald-400">
        <option value="">All phases</option>
        <?php foreach ($phases as $p): ?>
        <option value="<?= (int) $p['id'] ?>"><?= esc($p['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Bank Management Actions -->
    <div class="study-card p-4" data-reveal style="--d:.14s">
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-300 mb-2">
        <span data-lucide="folder-kanban" class="w-3.5 h-3.5 text-indigo-400"></span>
        <span>Question Bank</span>
      </div>
      <div class="flex flex-col gap-2">
        <button id="btn-new-mcq" class="btn btn-sm btn-success w-full justify-start gap-2 bg-emerald-600 hover:bg-emerald-500 border-emerald-500 text-white shadow-emerald-950/30 shadow-md">
          <span data-lucide="plus" class="w-4 h-4"></span>
          <span>Add Custom MCQ</span>
        </button>
        <button id="btn-bulk-mcq" class="btn btn-sm btn-outline w-full justify-start gap-2 border-white/10 hover:border-indigo-400 text-slate-300 hover:text-white">
          <span data-lucide="clipboard-list" class="w-4 h-4 text-indigo-400"></span>
          <span>Bulk JSON / TSV Import</span>
        </button>
      </div>
    </div>
  </aside>

  <!-- Quiz area -->
  <div data-reveal style="--d:.06s" class="mosaic">
    <div id="quiz-stage" class="study-card x-panel span-8 p-6 min-h-[380px] flex flex-col justify-center transition-all" data-tilt>
      <div class="text-center py-12">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shadow-[0_0_24px_rgba(99,102,241,0.15)] mb-4">
          <span data-lucide="crosshair" class="w-8 h-8"></span>
        </div>
        <h2 class="text-xl font-bold text-white tracking-tight">Select a Training Mode to Begin</h2>
        <p class="mt-1.5 text-sm text-slate-400 max-w-sm mx-auto">
          Choose a workout mode from the sidebar. You can use keyboard shortcuts <span class="font-mono text-xs text-indigo-300 bg-slate-800 px-1.5 py-0.5 rounded border border-slate-700">[1]-[4]</span> to select options and <span class="font-mono text-xs text-indigo-300 bg-slate-800 px-1.5 py-0.5 rounded border border-slate-700">[Enter]</span> to advance.
        </p>
      </div>
    </div>

    <!-- Arena rail -->
    <aside class="span-4 grid content-start gap-5">
      <div class="study-card x-panel p-5 hov-lift" data-tilt>
        <div class="x-title">
          <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Scoring</p>
          <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
        </div>
        <div class="x-content">
        <p class="mt-1 text-sm font-semibold text-white">+10 XP <span class="text-slate-400 font-normal">per correct</span></p>
        <p class="mt-1 text-xs text-slate-500">80%+ accuracy pops confetti · 100% fires the full show.</p>
        </div>
      </div>
      <div class="study-card x-panel p-5 hov-lift" data-tilt>
        <div class="x-title">
          <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Keys</p>
          <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
        </div>
        <div class="x-content">
        <p class="mt-1.5 flex flex-wrap gap-1.5 font-mono text-[11px]">
          <kbd class="rounded border border-white/10 bg-black/40 px-1.5 py-0.5 text-violet-300">1–4</kbd>
          <kbd class="rounded border border-white/10 bg-black/40 px-1.5 py-0.5 text-violet-300">Enter</kbd>
          <kbd class="rounded border border-white/10 bg-black/40 px-1.5 py-0.5 text-violet-300">/</kbd>
        </p>
        <p class="mt-1 text-xs text-slate-500">Answer, advance, search docs — never touch the mouse.</p>
        </div>
      </div>
      <a href="<?= $base ?>/challenge" class="study-card block p-5 hov-glow" data-tilt>
        <p class="text-[11px] font-bold uppercase tracking-wider text-amber-300">Daily Challenge</p>
        <p class="mt-1 text-sm font-semibold text-white">+100 XP bonus · 10 min · 10 Qs →</p>
      </a>
    </aside>

    <!-- MCQ manager -->
    <div class="study-card x-panel span-12 p-6 space-y-4" id="manager-container">
      <div class="x-title">
        <div class="flex items-center gap-2">
          <span data-lucide="layers" class="w-4 h-4 text-indigo-400"></span>
          <h2 class="text-base font-bold text-white">Active Questions List <span id="mcq-list-count" class="text-xs font-mono text-slate-400 ml-2"></span></h2>
        </div>
        <button id="btn-reload-list" class="btn btn-xs btn-ghost gap-1.5 text-slate-400 hover:text-white border border-white/5 hover:border-white/20">
          <span data-lucide="refresh-cw" class="w-3.5 h-3.5"></span>
          <span>Refresh</span>
        </button>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content">
      <div id="mcq-list" class="space-y-5"></div>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit MCQ dialog -->
<dialog id="mcq-dialog" class="modal modal-middle bg-slate-950/80 backdrop-blur-md text-slate-100">
  <form id="mcq-form" class="modal-box max-w-2xl bg-slate-900/95 border border-white/10 rounded-2xl shadow-2xl p-6">
    <div class="flex items-center justify-between border-b border-white/10 pb-4">
      <h3 id="mcq-form-title" class="text-lg font-bold text-white flex items-center gap-2">
        <span data-lucide="plus" class="w-5 h-5 text-indigo-400"></span>
        <span>Add Question</span>
      </h3>
      <span class="text-xs text-slate-400">LaTeX / KaTeX supported</span>
    </div>
    <input type="hidden" id="mcq-id">
    <div class="mt-4 space-y-3.5">
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label text-xs text-slate-400 font-medium py-1">Subject</label>
          <select id="mcq-subject" class="select select-bordered select-sm w-full bg-slate-800/90 border border-white/10" required>
            <option value="">— select subject —</option>
          </select>
        </div>
        <div>
          <label class="label text-xs text-slate-400 font-medium py-1">Difficulty</label>
          <select id="mcq-difficulty" class="select select-bordered select-sm w-full bg-slate-800/90 border border-white/10">
            <option value="E">Easy (1 Mark)</option>
            <option value="M" selected>Medium (1-2 Marks)</option>
            <option value="H">Hard (2 Marks GATE standard)</option>
          </select>
        </div>
      </div>
      <div>
        <label class="label text-xs text-slate-400 font-medium py-1">Phase (for AI generation)</label>
        <select id="mcq-phase" class="select select-bordered select-sm w-full bg-slate-800/90 border border-white/10">
          <option value="">All phases</option>
          <?php foreach ($phases as $p): ?>
          <option value="<?= (int) $p['id'] ?>"><?= esc($p['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="label text-xs text-slate-400 font-medium py-1">Question Body</label>
        <textarea id="mcq-question" class="textarea textarea-bordered w-full bg-slate-800/90 border border-white/10 text-sm" rows="3" placeholder="Write question statement. Use \(...\) or $$...$$ for math formulas." required></textarea>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label text-xs text-slate-400 font-medium py-1">Option A</label>
          <input id="mcq-opt-a" class="input input-bordered input-sm w-full bg-slate-800/90 border border-white/10" placeholder="Option A" required>
        </div>
        <div>
          <label class="label text-xs text-slate-400 font-medium py-1">Option B</label>
          <input id="mcq-opt-b" class="input input-bordered input-sm w-full bg-slate-800/90 border border-white/10" placeholder="Option B" required>
        </div>
        <div>
          <label class="label text-xs text-slate-400 font-medium py-1">Option C</label>
          <input id="mcq-opt-c" class="input input-bordered input-sm w-full bg-slate-800/90 border border-white/10" placeholder="Option C" required>
        </div>
        <div>
          <label class="label text-xs text-slate-400 font-medium py-1">Option D</label>
          <input id="mcq-opt-d" class="input input-bordered input-sm w-full bg-slate-800/90 border border-white/10" placeholder="Option D" required>
        </div>
      </div>
      <div class="grid grid-cols-[8rem_1fr] gap-3">
        <div>
          <label class="label text-xs text-slate-400 font-medium py-1">Correct Answer</label>
          <select id="mcq-answer" class="select select-bordered select-sm w-full bg-slate-800/90 border border-white/10 font-bold text-indigo-300">
            <option value="A">Option A</option>
            <option value="B">Option B</option>
            <option value="C">Option C</option>
            <option value="D">Option D</option>
          </select>
        </div>
        <div>
          <label class="label text-xs text-slate-400 font-medium py-1">Step-by-step Solution / Explanation</label>
          <textarea id="mcq-explanation" class="textarea textarea-bordered textarea-sm w-full bg-slate-800/90 border border-white/10" rows="2" placeholder="Explain derivation, core rule or why other options fail."></textarea>
        </div>
      </div>
    </div>
    <div class="modal-action border-t border-white/10 pt-4 mt-5 flex justify-between items-center">
      <button type="button" id="btn-mcq-ai" class="btn btn-sm btn-outline border-indigo-500/40 text-indigo-300 hover:bg-indigo-500/20 gap-2">
        <span data-lucide="bot" class="w-4 h-4"></span>
        <span>AI Generate Adaptive</span>
      </button>
      <div class="flex gap-2">
        <button type="button" id="mcq-cancel" class="btn btn-sm btn-ghost">Cancel</button>
        <button type="submit" class="btn btn-sm btn-primary">Save to Bank</button>
      </div>
    </div>
  </form>
</dialog>
