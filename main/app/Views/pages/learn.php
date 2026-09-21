<?php $base = rtrim(base_url(), '/'); ?>
<div class="grid gap-6 lg:grid-cols-3">
  <div class="lg:col-span-2 space-y-6" data-reveal>
    <!-- Breadcrumb & Topic Title Header -->
    <div class="study-card x-panel p-6">
      <div class="x-content">
      <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium" aria-label="Breadcrumb">
        <a href="<?= $base ?>/course" class="inline-flex items-center gap-1.5 hover:text-indigo-300 transition-colors">
          <span data-lucide="compass" class="w-3.5 h-3.5 text-indigo-400"></span>
          <span>Roadmap</span>
        </a>
        <span data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></span>
        <?php if ($topic['subject_name']): ?>
          <span class="inline-flex items-center gap-1.5 text-slate-300">
            <span class="w-2 h-2 rounded-full" style="background-color: <?= esc($topic['color'] ?: '#6366f1') ?>"></span>
            <span><?= esc($topic['subject_name']) ?></span>
          </span>
          <span data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></span>
        <?php endif; ?>
        <span class="text-indigo-300 font-semibold truncate"><?= esc($topic['name']) ?></span>
      </nav>

      <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3" data-split>
            <span class="p-2 rounded-xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400">
              <span data-lucide="book-open" class="w-6 h-6"></span>
            </span>
            <?= esc($topic['name']) ?>
          </h1>
          <?php if ($topic['subject_name']): ?>
            <p class="mt-2 text-sm text-slate-400 flex items-center gap-2">
              <span class="chip chip-quest text-xs">Core Syllabus</span>
              <span><?= esc($topic['subject_name']) ?></span>
            </p>
          <?php endif; ?>
        </div>

        <div class="flex items-center gap-2">
          <?php if ($topic['status'] === 'done'): ?>
            <span class="chip chip-focus text-xs">
              <span data-lucide="check-circle-2" class="w-3.5 h-3.5"></span>
              <span>Topic Completed</span>
            </span>
          <?php else: ?>
            <span class="chip chip-xp text-xs">
              <span data-lucide="play-circle" class="w-3.5 h-3.5"></span>
              <span>In Progress</span>
            </span>
          <?php endif; ?>
          <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
        </div>
      </div>
      </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
      <!-- Concept Card -->
      <div class="study-card x-panel p-5 hov-lift" data-tilt>
        <div class="x-title">
        <div class="flex items-center gap-2.5">
          <div class="p-1.5 rounded-lg bg-indigo-500/15 text-indigo-400" data-depth>
            <span data-lucide="brain" class="w-4 h-4"></span>
          </div>
          <h2 class="font-bold text-slate-100 text-base">Key Concept & Study Framework</h2>
        </div>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
        </div>
        <div class="x-content">
        <p class="mt-3 text-sm text-slate-300 leading-relaxed" data-quote>
          Open your primary notes or Notion study workspace for this topic. Master the fundamental theorems, solve derivations, then test your retention with the practice MCQs below.
        </p>
        <div class="mt-4 rounded-xl border border-indigo-500/25 bg-indigo-500/10 p-3.5">
          <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-300">How to master this</p>
          <ol class="mt-2 space-y-1.5 text-xs text-slate-300 list-decimal list-inside">
            <li>Read the theorem statement twice, slowly.</li>
            <li>Derive it once by hand, no peeking.</li>
            <li>Solve 5 PYQs using only this concept.</li>
            <li>Teach it back in one paragraph.</li>
          </ol>
        </div>
        </div>
      </div>

      <!-- KaTeX Formula Card -->
      <div id="katex-demo" class="study-card x-panel p-5 hov-lift" data-tilt>
        <div class="x-title">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2.5">
            <div class="p-1.5 rounded-lg bg-amber-500/15 text-amber-400" data-depth>
              <span data-lucide="sparkles" class="w-4 h-4"></span>
            </div>
            <h2 class="font-bold text-slate-100 text-base">Key Formula & Master Theorems</h2>
          </div>
          <span class="text-[11px] font-mono uppercase tracking-wider text-amber-400/80 bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">Auto KaTeX</span>
        </div>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
        </div>
        <div class="x-content">
        <div class="mt-4 p-4 rounded-xl bg-slate-950/60 border border-white/5 overflow-x-auto text-slate-100 text-lg leading-loose font-mono">
          \(T(n) = aT\!\left(\frac{n}{b}\right) + f(n)\)
          <br><span class="text-sm text-slate-400 font-sans">Standard Master Theorem:</span> \(\displaystyle T(n) = \Theta(n^{\log_b a})\)
        </div>
        <div class="mt-4 rounded-xl border border-amber-500/25 bg-amber-500/10 p-3.5">
          <p class="text-[11px] font-bold uppercase tracking-wider text-amber-300">When to use it</p>
          <ul class="mt-2 space-y-1.5 text-xs text-slate-300 list-disc list-inside">
            <li>Divide-and-conquer recurrences only.</li>
            <li>Check regularity condition for case 3.</li>
            <li>Watch out: floors, ceilings and non-polynomial gaps.</li>
          </ul>
        </div>
        </div>
      </div>

      <!-- Concept Diagram -->
      <div class="study-card x-panel p-5 xl:col-span-2 hov-lift" data-tilt>
        <div class="x-title">
        <div class="flex items-center gap-2.5">
          <div class="p-1.5 rounded-lg bg-emerald-500/15 text-emerald-400">
            <span data-lucide="activity" class="w-4 h-4"></span>
          </div>
          <h2 class="font-bold text-slate-100 text-base">Concept Knowledge Flow</h2>
        </div>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
        </div>
        <div class="x-content">
        <div class="mt-4 p-4 rounded-xl bg-slate-950/50 border border-white/5 flex items-center justify-center overflow-x-auto">
          <pre class="mermaid" id="concept-diagram">
flowchart LR
  A["📚 Syllabus Notes"] --> B["📖 Deep Study"]
  B --> C["✍️ Formula Drills"]
  C --> D["🧠 Test & Mastery"]
  style A fill:#1e1b4b,stroke:#818cf8,color:#e0e7ff
  style B fill:#0f172a,stroke:#38bdf8,color:#f0f9ff
  style C fill:#064e3b,stroke:#34d399,color:#ecfdf5
   style D fill:#78350f,stroke:#fbbf24,color:#fef3c7
</pre>
        </div>
        </div>
      </div>
    </div>

    <!-- Practice Section -->
    <div class="study-card x-panel p-6">
      <div class="x-title">
      <div class="flex items-center justify-between w-full">
        <div class="flex items-center gap-2.5">
          <div class="p-1.5 rounded-lg bg-indigo-500/15 text-indigo-400">
            <span data-lucide="target" class="w-4 h-4"></span>
          </div>
          <div>
            <h2 class="text-lg font-bold text-white">Topic Practice MCQs</h2>
            <?php if ($mcqs): ?>
              <p class="text-xs text-slate-400 mt-0.5"><?= count($mcqs) ?> targeted questions · instant answer breakdown</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content">

      <?php if ($mcqs): ?>
        <div id="course-quiz" class="mt-4 space-y-3" data-mcqs='<?= esc(json_encode(array_map(fn ($m) => [
          'id' => (int) $m['id'], 'q' => $m['question'], 'a' => $m['opt_a'], 'b' => $m['opt_b'],
          'c' => $m['opt_c'], 'd' => $m['opt_d'], 'k' => $m['answer'], 'x' => $m['explanation'],
        ], $mcqs), JSON_UNESCAPED_UNICODE), 'attr') ?>'>
          <p class="text-sm text-slate-500 py-6 text-center">Loading topic quiz…</p>
        </div>
      <?php else: ?>
        <div class="mt-4 rounded-xl border border-white/5 bg-slate-950/40 p-8 text-center">
          <div class="w-12 h-12 mx-auto rounded-full bg-slate-800/80 flex items-center justify-center text-slate-400 mb-3">
            <span data-lucide="help-circle" class="w-6 h-6"></span>
          </div>
          <p class="text-sm text-slate-300 font-medium">No MCQs linked directly to this topic yet.</p>
          <p class="text-xs text-slate-500 mt-1">Practice topic questions under Training Mode with subject filters.</p>
          <a href="<?= $base ?>/training?subject=<?= (int) ($topic['subject_id'] ?? 0) ?>" class="btn btn-sm btn-primary mt-4 inline-flex items-center gap-2">
            <span data-lucide="zap" class="w-4 h-4"></span>
            <span>Launch Training Mode →</span>
          </a>
        </div>
      <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Aside: Resources & Progress -->
  <aside class="space-y-6" data-reveal style="--d:.1s">
    <div class="study-card x-panel p-5">
      <div class="x-title">
        <div class="flex items-center gap-2 text-slate-200 font-semibold text-sm">
          <span data-lucide="file-text" class="w-4 h-4 text-indigo-400"></span>
          <span>Linked Resources</span>
        </div>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content">
      <ul class="mt-3 space-y-2.5 text-sm">
        <?php if (count($docs) === 0): ?>
          <li class="text-xs text-slate-500 py-2">No documents mapped to this topic.</li>
        <?php endif; ?>
        <?php foreach ($docs as $d): ?>
          <li>
            <?php if ($d['kind'] === 'pdf'): ?>
              <a href="<?= $base ?>/documents/stream/<?= (int) $d['id'] ?>" target="_blank" class="group flex items-center justify-between p-2.5 rounded-xl bg-slate-800/40 hover:bg-slate-800/80 border border-white/5 hover:border-indigo-500/30 transition-all text-slate-300 hover:text-white">
                <span class="inline-flex items-center gap-2 truncate pr-2">
                  <span data-lucide="file-text" class="w-4 h-4 text-rose-400 shrink-0"></span>
                  <span class="truncate"><?= esc($d['title']) ?></span>
                </span>
                <span data-lucide="external-link" class="w-3.5 h-3.5 text-slate-500 group-hover:text-indigo-400 shrink-0"></span>
              </a>
            <?php else: ?>
              <a href="<?= esc(safe_external_url($d['url'] ?? null)) ?>" target="_blank" rel="noopener" class="group flex items-center justify-between p-2.5 rounded-xl bg-slate-800/40 hover:bg-slate-800/80 border border-white/5 hover:border-indigo-500/30 transition-all text-slate-300 hover:text-white">
                <span class="inline-flex items-center gap-2 truncate pr-2">
                  <span data-lucide="external-link" class="w-4 h-4 text-indigo-400 shrink-0"></span>
                  <span class="truncate"><?= esc($d['title']) ?></span>
                </span>
                <span data-lucide="external-link" class="w-3.5 h-3.5 text-slate-500 group-hover:text-indigo-400 shrink-0"></span>
              </a>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
        <li>
          <a class="group flex items-center justify-between p-2.5 rounded-xl bg-amber-500/5 hover:bg-amber-500/10 border border-amber-500/20 text-amber-300 hover:text-amber-200 transition-all text-xs font-semibold" target="_blank" href="https://app.notion.com/p/GATE-CS-2027-80-90-Marks-Plan-3d5e1176b072819bae83e42557dc8f29">
            <span class="inline-flex items-center gap-2">
              <span data-lucide="target" class="w-4 h-4 text-amber-400"></span>
              <span>Open 2027 Notion Plan</span>
            </span>
            <span data-lucide="external-link" class="w-3.5 h-3.5 text-amber-400/70"></span>
          </a>
        </li>
      </ul>
      </div>
    </div>

    <!-- Progress Card -->
    <div class="study-card x-panel p-5 text-center">
      <div class="x-title">
        <div class="text-left">
          <h2 class="text-sm font-bold text-white">Topic Status</h2>
          <p class="text-xs text-slate-400">Completion syncs to Notion</p>
        </div>
        <span class="chip <?= $topic['status'] === 'done' ? 'chip-focus' : 'chip-quest' ?> text-xs">
          <?= $topic['status'] === 'done' ? '100% Mastered' : '0% Mastered' ?>
        </span>
        <div class="panel_toolbox"><button type="button" data-panel-collapse aria-label="Collapse panel"><span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span></button></div>
      </div>
      <div class="x-content">
      <div class="mt-5 flex justify-center">
        <div class="progress-ring transform scale-110" data-pct="<?= $topic['status'] === 'done' ? 100 : 0 ?>"></div>
      </div>
      <button id="btn-topic-done" data-id="<?= (int) $topic['id'] ?>" data-notion="<?= esc($topic['notion_id']) ?>"
        class="btn mt-5 w-full font-semibold shadow-lg <?= $topic['status'] === 'done' ? 'btn-success bg-emerald-600 hover:bg-emerald-500 border-emerald-500 shadow-emerald-950/50' : 'btn-primary bg-indigo-600 hover:bg-indigo-500 border-indigo-500 shadow-indigo-950/50' ?>">
        <?= $topic['status'] === 'done' ? '✓ Mastered & Completed' : 'Mark Topic Complete (+20 XP)' ?>
      </button>
      </div>
    </div>
  </aside>
</div>