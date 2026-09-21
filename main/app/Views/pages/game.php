<div class="space-y-6 animate__animated animate__fadeIn max-w-6xl mx-auto px-2 sm:px-4 py-3">

  <!-- TOP HEADER HUD -->
  <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 rounded-2xl border border-white/10 bg-slate-900/80 p-4 backdrop-blur-xl shadow-2xl relative overflow-hidden">
    <!-- Neon top highlight -->
    <div class="absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-violet-500 via-cyan-400 to-emerald-400 opacity-80"></div>

    <!-- Player Profile & State -->
    <div class="flex items-center gap-3.5">
      <div class="relative">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-600 via-fuchsia-600 to-indigo-600 flex items-center justify-center text-xl font-black text-white shadow-lg shadow-violet-500/30 border border-white/20">
          🐍
        </div>
        <span id="player-status-dot" class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full bg-slate-500 border-2 border-slate-900" title="Offline"></span>
      </div>
      <div>
        <div class="flex items-center gap-2">
          <h2 id="hud-player-name" class="text-base font-bold text-white tracking-wide">Guest Aspirant</h2>
          <button id="btn-change-user" class="btn btn-xs btn-ghost text-slate-400 hover:text-white px-1.5 py-0.5" title="Change Player">
            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
          </button>
        </div>
        <p class="text-xs font-mono text-slate-400 flex items-center gap-2">
          <span>Excel Sync: <span id="hud-sync-status" class="text-emerald-400 font-semibold">Active</span></span>
          <span class="text-slate-600">•</span>
          <span id="hud-save-time" class="text-slate-400">Ready</span>
        </p>
      </div>
    </div>

    <!-- Score & Level Stats -->
    <div class="grid grid-cols-4 gap-2 sm:gap-3 text-center">
      <!-- Score -->
      <div class="rounded-xl border border-violet-500/20 bg-violet-500/10 px-3 py-1.5 min-w-[70px]">
        <p class="text-[10px] uppercase font-bold tracking-wider text-violet-300">Score</p>
        <p id="hud-score" class="text-lg sm:text-xl font-black font-mono text-white">0</p>
      </div>
      <!-- Level -->
      <div class="rounded-xl border border-cyan-500/20 bg-cyan-500/10 px-3 py-1.5 min-w-[65px]">
        <p class="text-[10px] uppercase font-bold tracking-wider text-cyan-300">Level</p>
        <p id="hud-level" class="text-lg sm:text-xl font-black font-mono text-cyan-200">1</p>
      </div>
      <!-- High Score -->
      <div class="rounded-xl border border-amber-500/20 bg-amber-500/10 px-3 py-1.5 min-w-[70px]">
        <p class="text-[10px] uppercase font-bold tracking-wider text-amber-300">Best</p>
        <p id="hud-highscore" class="text-lg sm:text-xl font-black font-mono text-amber-200">0</p>
      </div>
      <!-- Length -->
      <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-3 py-1.5 min-w-[65px]">
        <p class="text-[10px] uppercase font-bold tracking-wider text-emerald-300">Length</p>
        <p id="hud-length" class="text-lg sm:text-xl font-black font-mono text-emerald-200">3</p>
      </div>
    </div>

    <!-- Controls / Action Buttons -->
    <div class="flex items-center justify-end gap-2">
      <button id="btn-sound-toggle" class="btn btn-sm btn-circle btn-ghost border border-white/10 text-slate-300 hover:text-white hover:bg-white/10" title="Toggle Sound (M)">
        <i data-lucide="volume-2" id="icon-sound" class="w-4 h-4"></i>
      </button>
      <button id="btn-pause-toggle" class="btn btn-sm btn-circle btn-ghost border border-white/10 text-slate-300 hover:text-white hover:bg-white/10" title="Pause / Resume (Space)">
        <i data-lucide="play" id="icon-pause" class="w-4 h-4"></i>
      </button>
      <button id="btn-restart" class="btn btn-sm btn-circle btn-ghost border border-white/10 text-slate-300 hover:text-white hover:bg-white/10" title="Restart Game (R)">
        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
      </button>
      <button id="btn-help-toggle" class="btn btn-sm btn-circle btn-ghost border border-white/10 text-slate-300 hover:text-white hover:bg-white/10" title="Instructions">
        <i data-lucide="help-circle" class="w-4 h-4"></i>
      </button>
      <button id="btn-debug-toggle" class="btn btn-sm btn-ghost text-slate-500 hover:text-slate-300 px-2 text-xs font-mono" title="Toggle Dev Debugger">
        DBG
      </button>
    </div>
  </div>

  <!-- MAIN GAME VIEWPORT GRID -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    
    <!-- CANVAS ARENA CONTAINER (8 Cols) -->
    <div class="lg:col-span-8 flex flex-col items-center">
      <div id="game-arena-wrapper" class="relative w-full aspect-square max-w-[620px] rounded-3xl border-2 border-violet-500/30 bg-slate-950/90 shadow-[0_0_50px_rgba(124,58,237,0.2)] overflow-hidden backdrop-blur-md select-none touch-none">
        
        <!-- Canvas Element -->
        <canvas id="game-canvas" class="w-full h-full block cursor-crosshair"></canvas>

        <!-- Dynamic In-Arena Flash Overlays -->
        <div id="arena-flash-effect" class="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-300"></div>

        <!-- LEVEL UP BANNER OVERLAY -->
        <div id="overlay-level-up" class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center bg-slate-950/70 backdrop-blur-sm opacity-0 transition-all duration-300 scale-90 z-20">
          <div class="text-center p-6 space-y-2">
            <span class="text-4xl">⚡</span>
            <h3 class="text-3xl sm:text-4xl font-black tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-orange-400 to-yellow-200 animate-pulse">
              LEVEL UP!
            </h3>
            <p id="levelup-subtext" class="text-sm font-semibold text-cyan-300">Speed & Multipliers Increased</p>
          </div>
        </div>

        <!-- PAUSE OVERLAY -->
        <div id="overlay-pause" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/80 backdrop-blur-md z-20 hidden">
          <div class="text-center space-y-4 p-6 max-w-sm rounded-2xl border border-white/10 bg-slate-900/90 shadow-2xl">
            <span class="text-3xl">⏸️</span>
            <h3 class="text-2xl font-black text-white">GAME PAUSED</h3>
            <p class="text-xs text-slate-400">Take a breath, aspirant. Press Space or tap Resume when ready.</p>
            <div class="flex flex-col gap-2 pt-2">
              <button id="btn-overlay-resume" class="btn btn-primary btn-sm rounded-xl font-bold">
                <i data-lucide="play" class="w-4 h-4"></i> Resume Game
              </button>
              <button id="btn-overlay-restart" class="btn btn-outline btn-sm rounded-xl text-slate-300">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Restart Fresh
              </button>
            </div>
          </div>
        </div>

        <!-- GAME OVER OVERLAY -->
        <div id="overlay-game-over" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/90 backdrop-blur-md z-30 hidden">
          <div class="text-center space-y-4 p-6 sm:p-8 max-w-md w-full mx-4 rounded-3xl border border-rose-500/30 bg-slate-900/95 shadow-2xl shadow-rose-950/50">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-rose-500/20 border border-rose-500/30 text-rose-400 text-3xl">
              💥
            </div>
            <div>
              <h3 class="text-2xl sm:text-3xl font-black text-white tracking-wide">GAME OVER</h3>
              <p id="gameover-cause" class="text-xs text-rose-300/80 mt-1">Collision Detected</p>
            </div>

            <!-- Stats Summary -->
            <div class="grid grid-cols-3 gap-2 py-2">
              <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                <p class="text-[10px] font-bold text-slate-400 uppercase">Score</p>
                <p id="go-score" class="text-xl font-black font-mono text-white">0</p>
              </div>
              <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                <p class="text-[10px] font-bold text-slate-400 uppercase">Level</p>
                <p id="go-level" class="text-xl font-black font-mono text-cyan-300">1</p>
              </div>
              <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                <p class="text-[10px] font-bold text-slate-400 uppercase">Length</p>
                <p id="go-length" class="text-xl font-black font-mono text-emerald-300">3</p>
              </div>
            </div>

            <p id="go-record-msg" class="text-xs font-semibold text-amber-300 hidden">🏆 New Personal High Score!</p>

            <div class="flex gap-3 pt-2">
              <button id="btn-go-play-again" class="btn btn-primary flex-1 rounded-xl font-bold shadow-lg shadow-violet-600/30">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Play Again
              </button>
            </div>
          </div>
        </div>

        <!-- START / USERNAME MODAL OVERLAY -->
        <div id="overlay-start" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/95 backdrop-blur-md z-40">
          <div class="text-center space-y-4 p-6 sm:p-8 max-w-md w-full mx-4 rounded-3xl border border-violet-500/30 bg-slate-900/90 shadow-2xl">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-violet-600/20 border border-violet-500/30 text-3xl shadow-inner">
              🐍
            </div>
            <div>
              <h3 class="text-2xl sm:text-3xl font-black text-white">SNAKE CHASE BALL</h3>
              <p class="text-xs text-slate-400 mt-1">Arcade study break • Excel session storage</p>
            </div>

            <!-- Username Form -->
            <form id="form-start-game" class="space-y-3 pt-2 text-left">
              <div>
                <label for="input-username" class="block text-xs font-semibold text-slate-300 mb-1">Enter your player name:</label>
                <input type="text" id="input-username" class="input input-bordered w-full bg-slate-950/80 text-white rounded-xl border-white/20 focus:border-violet-500 font-medium" placeholder="e.g. Vishnu" maxlength="25" required autofocus>
              </div>

              <div id="resume-prompt-box" class="p-3 rounded-xl bg-violet-500/10 border border-violet-500/20 text-xs text-slate-300 hidden">
                <p class="font-bold text-violet-300 mb-1">Active Game Found!</p>
                <p id="resume-prompt-stats" class="text-slate-400">Level 2 • Score 150</p>
                <div class="flex gap-2 mt-2">
                  <button type="button" id="btn-action-resume" class="btn btn-xs btn-primary font-bold">Resume</button>
                  <button type="button" id="btn-action-new" class="btn btn-xs btn-outline text-slate-300">Start Fresh</button>
                </div>
              </div>

              <button type="submit" id="btn-start-submit" class="btn btn-primary w-full rounded-xl font-bold shadow-lg shadow-violet-600/30">
                START GAME
              </button>
            </form>
          </div>
        </div>

        <!-- IN-GAME DEBUG HUD (HIDDEN BY DEFAULT) -->
        <div id="debug-panel" class="absolute top-2 left-2 p-2 rounded-lg bg-black/80 border border-emerald-500/30 font-mono text-[10px] text-emerald-400 pointer-events-none z-10 hidden">
          <div>FPS: <span id="dbg-fps">60</span></div>
          <div>STATE: <span id="dbg-state">READY</span></div>
          <div>HEAD: <span id="dbg-head">0,0</span></div>
          <div>BALL: <span id="dbg-ball">0,0</span></div>
          <div>DIR: <span id="dbg-dir">RIGHT</span></div>
          <div>SPEED: <span id="dbg-speed">120ms</span></div>
        </div>

      </div>

      <!-- VIRTUAL MOBILE CONTROLLER (D-PAD) -->
      <div id="mobile-controls" class="w-full max-w-sm mt-4 p-4 rounded-2xl border border-white/10 bg-slate-900/60 backdrop-blur-sm flex flex-col items-center justify-center select-none touch-none">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-2">Virtual D-Pad</p>
        <div class="grid grid-cols-3 gap-2 w-48 h-48">
          <div></div>
          <button type="button" data-dir="UP" class="dpad-btn btn btn-neutral h-full rounded-2xl text-xl font-black active:scale-95 border-white/10 bg-slate-800 text-cyan-300 flex items-center justify-center">
            ▲
          </button>
          <div></div>

          <button type="button" data-dir="LEFT" class="dpad-btn btn btn-neutral h-full rounded-2xl text-xl font-black active:scale-95 border-white/10 bg-slate-800 text-cyan-300 flex items-center justify-center">
            ◀
          </button>
          <div class="flex items-center justify-center text-slate-600 font-bold text-xs">●</div>
          <button type="button" data-dir="RIGHT" class="dpad-btn btn btn-neutral h-full rounded-2xl text-xl font-black active:scale-95 border-white/10 bg-slate-800 text-cyan-300 flex items-center justify-center">
            ▶
          </button>

          <div></div>
          <button type="button" data-dir="DOWN" class="dpad-btn btn btn-neutral h-full rounded-2xl text-xl font-black active:scale-95 border-white/10 bg-slate-800 text-cyan-300 flex items-center justify-center">
            ▼
          </button>
          <div></div>
        </div>
      </div>
    </div>

    <!-- SIDEBAR: INSTRUCTIONS & EXCEL LEADERBOARD (4 Cols) -->
    <div class="lg:col-span-4 space-y-4">
      
      <!-- Instructions Card -->
      <div class="rounded-2xl border border-white/10 bg-slate-900/80 p-5 backdrop-blur-xl shadow-xl space-y-3">
        <div class="flex items-center gap-2 text-white font-bold text-sm">
          <i data-lucide="compass" class="w-4 h-4 text-violet-400"></i>
          <span>How To Play</span>
        </div>
        <ul class="text-xs text-slate-300 space-y-2">
          <li class="flex items-start gap-2">
            <span class="text-violet-400 font-bold">1.</span>
            <span>Guide the snake with <b class="text-white">Arrow Keys</b> or <b class="text-white">WASD</b>.</span>
          </li>
          <li class="flex items-start gap-2">
            <span class="text-cyan-400 font-bold">2.</span>
            <span>Chase and devour glowing <b class="text-amber-300">Energy Balls</b> to grow.</span>
          </li>
          <li class="flex items-start gap-2">
            <span class="text-emerald-400 font-bold">3.</span>
            <span>Don't hit arena walls or your own tail.</span>
          </li>
          <li class="flex items-start gap-2">
            <span class="text-amber-400 font-bold">4.</span>
            <span>Press <b class="text-white">Space</b> to pause, <b class="text-white">R</b> to restart.</span>
          </li>
          <li class="flex items-start gap-2">
            <span class="text-pink-400 font-bold">5.</span>
            <span>Progress automatically syncs to local <b class="text-emerald-300">Excel Database</b>.</span>
          </li>
        </ul>
      </div>

      <!-- Live Excel Leaderboard -->
      <div class="rounded-2xl border border-white/10 bg-slate-900/80 p-5 backdrop-blur-xl shadow-xl space-y-3">
        <div class="flex items-center justify-between text-white font-bold text-sm">
          <div class="flex items-center gap-2">
            <i data-lucide="trophy" class="w-4 h-4 text-amber-400"></i>
            <span>Hall of Fame</span>
          </div>
          <span class="text-[10px] font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">Excel DB</span>
        </div>

        <div id="leaderboard-container" class="space-y-2">
          <?php if (!empty($leaderboard)): ?>
            <?php foreach ($leaderboard as $lb): ?>
              <div class="flex items-center justify-between p-2.5 rounded-xl border border-white/5 bg-slate-950/60 text-xs">
                <div class="flex items-center gap-2.5 min-w-0">
                  <span class="w-5 h-5 rounded-full font-bold text-[10px] flex items-center justify-center <?= $lb['rank'] === 1 ? 'bg-amber-400 text-slate-950' : ($lb['rank'] === 2 ? 'bg-slate-300 text-slate-950' : 'bg-slate-800 text-slate-300') ?>">
                    <?= $lb['rank'] ?>
                  </span>
                  <span class="font-semibold text-slate-200 truncate"><?= esc($lb['username']) ?></span>
                </div>
                <div class="text-right font-mono">
                  <span class="font-bold text-amber-300"><?= number_format($lb['highScore']) ?></span>
                  <span class="text-[10px] text-slate-500 block">Lv <?= $lb['currentLevel'] ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-xs text-slate-500 py-3 text-center">No player records in Excel yet. Play the first game!</p>
          <?php endif; ?>
        </div>
      </div>

    </div>

  </div>

</div>
