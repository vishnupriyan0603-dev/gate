<?php
$storedToken = $storedToken ?? '';
?>
<div class="space-y-6">
  <!-- Page Header -->
  <div class="quest-hero p-6 flex flex-wrap items-center justify-between gap-4" data-reveal data-tilt>
    <div id="particles-hero" data-particles aria-hidden="true"></div>
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
        <span class="p-2 rounded-xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400">
          <span data-lucide="sliders" class="w-6 h-6"></span>
        </span>
        <span>System <span class="text-quest">Settings</span></span>
      </h1>
      <p class="mt-1 text-sm text-slate-400" data-quote>Live Notion synchronization, Groq AI inference parameters, and study targets.</p>
    </div>
    <span class="chip chip-focus text-xs">System Healthy</span>
  </div>

  <!-- Notion + AI mosaic -->
  <div class="mosaic">
  <!-- Notion Integration Section -->
  <section class="study-card x-panel span-7 p-6" data-reveal data-tilt>
    <div class="x-title">
      <div class="flex items-center gap-2.5">
        <div class="p-2 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20">
          <span data-lucide="target" class="w-5 h-5"></span>
        </div>
        <div>
          <h2 class="text-base font-bold text-white">Notion Dual-Sync Workspace</h2>
          <p class="text-xs text-slate-400">Two-way sync: master plan, study phases, calendar days and to-do checklists.</p>
        </div>
      </div>
      <span class="chip chip-xp text-xs">Active Integration</span>
      <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
    </div>
    <div class="x-content">

    <form id="settings-form" class="mt-5 space-y-4">
      <div>
        <label class="label text-xs font-semibold text-slate-300">Internal Integration Token</label>
        <input id="set-token" type="password" class="input input-bordered w-full bg-slate-900/90 border border-white/10 rounded-xl focus:border-indigo-400 font-mono text-sm text-slate-100" value="<?= esc($storedToken) ?>" placeholder="ntn_...">
      </div>

      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="label text-xs font-semibold text-slate-300">Master Plan Page ID</label>
          <input id="set-plan" class="input input-bordered w-full bg-slate-900/90 border border-white/10 rounded-xl focus:border-indigo-400 font-mono text-sm text-slate-100" value="<?= esc($planPage) ?>" placeholder="32-character page id">
        </div>
        <div>
          <label class="label text-xs font-semibold text-slate-300">Calendar Database Page ID</label>
          <input id="set-cal" class="input input-bordered w-full bg-slate-900/90 border border-white/10 rounded-xl focus:border-indigo-400 font-mono text-sm text-slate-100" value="<?= esc($calPage) ?>" placeholder="32-character page id">
        </div>
      </div>

      <div class="grid gap-4 sm:grid-cols-3">
        <div>
          <label class="label text-xs font-semibold text-slate-300">Target Exam Date</label>
          <input id="set-exam" type="date" class="input input-bordered w-full bg-slate-900/90 border border-white/10 rounded-xl focus:border-indigo-400 text-sm text-slate-100" value="<?= esc($examDate) ?>">
        </div>
        <div>
          <label class="label text-xs font-semibold text-slate-300">Target Score (Marks)</label>
          <input id="set-target" type="number" class="input input-bordered w-full bg-slate-900/90 border border-white/10 rounded-xl focus:border-indigo-400 font-mono text-sm text-slate-100" value="<?= esc($targetScore) ?>">
        </div>
        <div>
          <label class="label text-xs font-semibold text-slate-300">Weekly Target Hours</label>
          <input id="set-hours" type="number" class="input input-bordered w-full bg-slate-900/90 border border-white/10 rounded-xl focus:border-indigo-400 font-mono text-sm text-slate-100" value="<?= esc($weeklyHours) ?>">
        </div>
      </div>

      <div class="pt-2 flex flex-wrap gap-3">
        <button type="submit" class="btn btn-sm btn-primary bg-indigo-600 hover:bg-indigo-500 border-indigo-500 text-white shadow-lg shadow-indigo-950/50">Save Settings</button>
        <button type="button" id="btn-notion-sync" class="btn btn-sm btn-warning bg-amber-500 hover:bg-amber-400 border-amber-400 text-slate-950 font-bold gap-2">
          <span data-lucide="refresh-cw" class="w-4 h-4"></span>
          <span>Sync Notion Now</span>
        </button>
      </div>
    </form>
    <div id="sync-status" class="mt-4 text-sm font-medium"></div>
    </div>
  </section>

  <!-- Groq AI Configuration Section -->
  <section class="study-card x-panel span-5 p-6" data-reveal data-tilt>
    <div class="x-title">
      <div class="flex items-center gap-2.5">
        <div class="p-2 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
          <span data-lucide="bot" class="w-5 h-5"></span>
        </div>
        <div>
          <h2 class="text-base font-bold text-white">Groq AI Inference Engine</h2>
          <p class="text-xs text-slate-400">Powers adaptive question generation and real-time conceptual tutor. Stored securely on server.</p>
        </div>
      </div>
      <span class="chip chip-quest text-xs">High-Throughput</span>
      <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
    </div>
    <div class="x-content">

    <form id="ai-settings-form" class="mt-5 space-y-4">
      <div>
        <label class="label text-xs font-semibold text-slate-300">Groq API Key</label>
        <input id="set-groq-key" type="password" class="input input-bordered w-full bg-slate-900/90 border border-white/10 rounded-xl focus:border-indigo-400 font-mono text-sm text-slate-100" value="<?= esc($storedGroqKey ?? '') ?>" placeholder="gsk_...">
      </div>

      <div>
        <label class="label text-xs font-semibold text-slate-300">Preferred Reasoning Model</label>
        <select id="set-groq-model" class="select select-bordered w-full bg-slate-900/90 border border-white/10 rounded-xl focus:border-indigo-400 text-sm text-slate-200">
          <?php foreach (['openai/gpt-oss-120b' => 'GPT-OSS 120B (recommended for GATE depth)', 'openai/gpt-oss-20b' => 'GPT-OSS 20B (ultra fast)', 'groq/compound-mini' => 'Compound Mini (fast / cost-efficient)', 'qwen/qwen3.6-27b' => 'Qwen 3.6 27B', 'groq/compound' => 'Compound Standard'] as $mid => $mlabel) : ?>
            <option value="<?= esc($mid) ?>" <?= ($groqModel ?? '') === $mid ? 'selected' : '' ?>><?= esc($mlabel) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="pt-2 flex flex-wrap gap-3">
        <button type="submit" class="btn btn-sm btn-primary bg-indigo-600 hover:bg-indigo-500 border-indigo-500 text-white shadow-lg shadow-indigo-950/50">Save AI Configuration</button>
        <button type="button" id="btn-groq-test" class="btn btn-sm btn-outline border-white/10 text-slate-300 hover:text-white gap-2">
          <span data-lucide="zap" class="w-4 h-4 text-amber-400"></span>
          <span>Test API Latency</span>
        </button>
      </div>
    </form>
    <div id="ai-status" class="mt-4 text-sm font-medium"></div>
    </div>
  </section>

  </div>

  <!-- Database + Checklists mosaic -->
  <div class="mosaic">
  <!-- Database & Architecture Info -->
  <section class="study-card x-panel span-5 p-6" data-reveal data-tilt>
    <div class="x-title">
      <div class="flex items-center gap-2.5">
        <div class="p-2 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
          <span data-lucide="database" class="w-5 h-5"></span>
        </div>
        <div>
          <h2 class="text-base font-bold text-white">Database & Knowledge Graph</h2>
          <p class="text-xs text-slate-400">Relational schema and entity tallies in local MariaDB instance.</p>
        </div>
      </div>
      <span class="chip chip-quest text-xs font-mono">MariaDB / CI4</span>
      <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
    </div>
    <div class="x-content">

    <div class="mt-4 grid gap-5 grid-cols-2 text-center">
      <div class="rounded-xl bg-slate-900/80 border border-white/5 p-4">
        <p class="text-[11px] font-mono uppercase tracking-wider text-slate-400">Subjects</p>
        <p class="text-xl font-bold text-white mt-1"><?= (int) $nSubjects ?></p>
        <p class="mt-1 text-[10px] text-slate-500">Syllabus domains under quest</p>
      </div>
      <div class="rounded-xl bg-slate-900/80 border border-white/5 p-4">
        <p class="text-[11px] font-mono uppercase tracking-wider text-slate-400">Phases</p>
        <p class="text-xl font-bold text-white mt-1"><?= (int) $nPhases ?></p>
        <p class="mt-1 text-[10px] text-slate-500">Roadmap stages sequenced</p>
      </div>
      <div class="rounded-xl bg-slate-900/80 border border-white/5 p-4">
        <p class="text-[11px] font-mono uppercase tracking-wider text-slate-400">Topics</p>
        <p class="text-xl font-bold text-white mt-1"><?= (int) $nTopics ?></p>
        <p class="mt-1 text-[10px] text-slate-500">Concepts queued to master</p>
      </div>
      <div class="rounded-xl bg-slate-900/80 border border-white/5 p-4">
        <p class="text-[11px] font-mono uppercase tracking-wider text-slate-400">MCQs in Bank</p>
        <p class="text-xl font-bold text-emerald-400 mt-1"><?= (int) $nMcqs ?></p>
        <p class="mt-1 text-[10px] text-slate-500">Practice questions banked</p>
      </div>
    </div>

    <div class="mt-5 flex justify-end">
      <button id="tour-replay" class="btn btn-sm btn-ghost border border-white/10 hover:border-indigo-400 text-slate-300 hover:text-white gap-2">
        <span data-lucide="compass" class="w-4 h-4 text-indigo-400"></span>
        <span>Replay Guided Onboarding Tour</span>
      </button>
    </div>
    </div>
  </section>

  <!-- Daily Routine Checklists -->
  <section class="study-card x-panel span-7 p-6" data-reveal data-tilt>
    <div class="x-title">
      <div class="flex items-center gap-2.5">
        <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
          <span data-lucide="clipboard-list" class="w-5 h-5"></span>
        </div>
        <div>
          <h2 class="text-base font-bold text-white">Daily Routine & Habit Checklists</h2>
          <p class="text-xs text-slate-400"><?= (int) $checklistCount ?> active daily items synced from Notion plan.</p>
        </div>
      </div>
      <span class="chip chip-focus text-xs">Live Sync</span>
      <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
    </div>
    <div class="x-content">
    <div id="checklists" class="mt-4 space-y-2.5"></div>
    </div>
  </section>
  </div>
</div>
