import { GameState } from './GameState';
import { Snake } from './Snake';
import { BallManager } from './Ball';
import { CollisionSystem } from './CollisionSystem';
import { ParticleSystem } from './ParticleSystem';
import { AudioManager } from './AudioManager';
import { CanvasRenderer } from './CanvasRenderer';
import { InputHandler } from './InputHandler';
import { SessionManager } from './SessionManager';
import { GameLoop } from './GameLoop';
import { Direction } from './types';
import { GAME_CONFIG } from './GameConfig';

export class Game {
  private canvas: HTMLCanvasElement;
  private state: GameState;
  private snake: Snake;
  private ballManager: BallManager;
  private particles: ParticleSystem;
  private audio: AudioManager;
  private renderer: CanvasRenderer;
  private input: InputHandler;
  private session: SessionManager;
  private loop: GameLoop;

  private checkpointTimer: number | null = null;
  private isDebugVisible: boolean = false;

  constructor(canvas: HTMLCanvasElement) {
    this.canvas = canvas;
    this.state = new GameState();
    this.snake = new Snake();
    this.ballManager = new BallManager();
    this.particles = new ParticleSystem();
    this.audio = new AudioManager();
    this.renderer = new CanvasRenderer(canvas);
    this.session = new SessionManager();

    this.input = new InputHandler({
      onDirection: this.handleDirection.bind(this),
      onPauseToggle: this.togglePause.bind(this),
      onRestart: this.restart.bind(this),
      onDebugToggle: this.toggleDebug.bind(this),
    });
    this.input.bindCanvas(canvas);

    this.loop = new GameLoop(
      this.update.bind(this),
      this.render.bind(this),
      () => this.state.currentLevelConfig.stepIntervalMs,
    );

    this.initUI();
    this.initSessionPrompt();

    window.addEventListener('resize', () => this.renderer.handleResize());
  }

  private initUI(): void {
    // Sound Button
    const btnSound = document.getElementById('btn-sound-toggle');
    const iconSound = document.getElementById('icon-sound');
    if (btnSound) {
      btnSound.addEventListener('click', () => {
        const enabled = this.audio.toggle();
        btnSound.classList.toggle('text-rose-400', !enabled);
        if (iconSound) {
          iconSound.setAttribute('data-lucide', enabled ? 'volume-2' : 'volume-x');
        }
      });
    }

    // Pause Buttons
    document.getElementById('btn-pause-toggle')?.addEventListener('click', () => this.togglePause());
    document.getElementById('btn-overlay-resume')?.addEventListener('click', () => this.togglePause());

    // Restart Buttons
    document.getElementById('btn-restart')?.addEventListener('click', () => this.restart());
    document.getElementById('btn-overlay-restart')?.addEventListener('click', () => this.restart());
    document.getElementById('btn-go-play-again')?.addEventListener('click', () => this.restart());

    // Debug Button
    document.getElementById('btn-debug-toggle')?.addEventListener('click', () => this.toggleDebug());

    // Change User Button
    document.getElementById('btn-change-user')?.addEventListener('click', () => {
      this.showStartModal();
    });

    // Subscribe to state changes to update HUD
    this.state.onStateChange((s) => {
      const hudScore = document.getElementById('hud-score');
      const hudLevel = document.getElementById('hud-level');
      const hudHigh = document.getElementById('hud-highscore');
      const hudLen = document.getElementById('hud-length');
      const hudName = document.getElementById('hud-player-name');

      if (hudScore) hudScore.textContent = String(s.score);
      if (hudLevel) hudLevel.textContent = String(s.level);
      if (hudHigh) hudHigh.textContent = String(s.highScore);
      if (hudLen) hudLen.textContent = String(s.snakeLength);
      if (hudName) hudName.textContent = s.playerName;
    });
  }

  private async initSessionPrompt(): Promise<void> {
    const savedUser = this.session.getUsername();
    const inputUser = document.getElementById('input-username') as HTMLInputElement | null;
    const formStart = document.getElementById('form-start-game');

    if (savedUser && inputUser) {
      inputUser.value = savedUser;
      // Check if user has active session
      try {
        const res = await this.session.startSession(savedUser);
        if (res.canResume) {
          this.showResumeOption(res.player);
          return;
        }
      } catch {
        /* ignore */
      }
    }

    this.showStartModal();

    if (formStart) {
      formStart.addEventListener('submit', async (e) => {
        e.preventDefault();
        const username = inputUser?.value.trim() || 'Aspirant';
        await this.authenticateAndStart(username);
      });
    }
  }

  private showStartModal(): void {
    const modal = document.getElementById('overlay-start');
    if (modal) modal.classList.remove('hidden');
    this.loop.stop();
  }

  private hideStartModal(): void {
    const modal = document.getElementById('overlay-start');
    if (modal) modal.classList.add('hidden');
  }

  private showResumeOption(player: any): void {
    const box = document.getElementById('resume-prompt-box');
    const stats = document.getElementById('resume-prompt-stats');
    const btnResume = document.getElementById('btn-action-resume');
    const btnNew = document.getElementById('btn-action-new');

    if (box && stats) {
      box.classList.remove('hidden');
      stats.textContent = `Level ${player.Current_Level} • Score ${player.Current_Score} • Length ${player.Snake_Length}`;

      btnResume?.addEventListener('click', () => {
        this.state.setPlayerData(player);
        this.snake.grow(Math.max(0, (player.Snake_Length || 3) - 3));
        this.hideStartModal();
        this.startCountdownAndPlay();
      }, { once: true });

      btnNew?.addEventListener('click', async () => {
        await this.session.resetNewGame();
        this.state.setPlayerData({ ...player, Current_Score: 0, Current_Level: 1, Snake_Length: 3 });
        this.hideStartModal();
        this.startCountdownAndPlay();
      }, { once: true });
    }
  }

  private async authenticateAndStart(username: string): Promise<void> {
    try {
      const res = await this.session.startSession(username);
      this.state.setPlayerData(res.player);

      if (res.canResume) {
        this.showResumeOption(res.player);
        return;
      }

      this.hideStartModal();
      this.startCountdownAndPlay();
    } catch (err: any) {
      alert(err.message || 'Failed to start session');
    }
  }

  private startCountdownAndPlay(): void {
    this.snake.reset();
    this.ballManager.spawn(this.snake);
    this.state.setStatus('PLAYING');
    this.loop.start();
    this.startAutoCheckpoint();

    const dot = document.getElementById('player-status-dot');
    if (dot) dot.className = 'absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-slate-900';
  }

  private handleDirection(dir: Direction): void {
    if (this.state.status === 'PAUSED') {
      this.togglePause();
    }
    if (this.state.status === 'PLAYING') {
      this.snake.queueDirection(dir);
    }
  }

  public togglePause(): void {
    if (this.state.status === 'PLAYING') {
      this.state.setStatus('PAUSED');
      this.audio.playPause();
      this.loop.stop();
      document.getElementById('overlay-pause')?.classList.remove('hidden');
      const icon = document.getElementById('icon-pause');
      if (icon) icon.setAttribute('data-lucide', 'play');
    } else if (this.state.status === 'PAUSED') {
      this.state.setStatus('PLAYING');
      this.audio.playPause();
      document.getElementById('overlay-pause')?.classList.add('hidden');
      const icon = document.getElementById('icon-pause');
      if (icon) icon.setAttribute('data-lucide', 'pause');
      this.loop.start();
    }
  }

  public restart(): void {
    document.getElementById('overlay-game-over')?.classList.add('hidden');
    document.getElementById('overlay-pause')?.classList.add('hidden');

    this.snake.reset();
    this.ballManager.spawn(this.snake);
    this.particles.clear();
    this.state.reset();
    this.state.setStatus('PLAYING');
    this.audio.playClick();
    this.loop.start();

    void this.session.resetNewGame();
  }

  private update(dt: number): void {
    if (this.state.status !== 'PLAYING') return;

    // Move snake
    this.snake.move();
    this.ballManager.updateAnimation(dt);
    this.particles.update();

    // 1. Wall Collision Check
    if (CollisionSystem.checkWallCollision(this.snake)) {
      this.handleGameOver('Hit Arena Wall Boundary');
      return;
    }

    // 2. Self Collision Check
    if (CollisionSystem.checkSelfCollision(this.snake)) {
      this.handleGameOver('Collided With Own Body');
      return;
    }

    // 3. Ball Pickup Check
    if (CollisionSystem.checkBallCollision(this.snake, this.ballManager)) {
      this.handleBallPickup();
    }

    this.state.snakeLength = this.snake.length;
    this.updateDebugStats();
  }

  private handleBallPickup(): void {
    const ball = this.ballManager.ball;
    const tSize = this.canvas.width / (window.devicePixelRatio || 1) / GAME_CONFIG.gridSize;

    // Emit particle burst & sound
    const px = (ball.x + 0.5) * tSize;
    const py = (ball.y + 0.5) * tSize;
    this.particles.emitBurst(px, py, '#f59e0b', 20);
    this.audio.playEat();

    // Grow snake
    this.snake.grow(1);

    // Add score & check level up
    const leveledUp = this.state.addScore(this.state.currentLevelConfig.ballPoints);

    if (leveledUp) {
      this.handleLevelUp();
    }

    // Spawn next ball safely
    this.ballManager.spawn(this.snake);

    // Trigger immediate checkpoint on milestones
    if (this.state.score % 50 === 0) {
      void this.triggerCheckpoint();
    }
  }

  private handleLevelUp(): void {
    this.audio.playLevelUp();

    // Trigger visual confetti if available
    if (typeof (window as any).confetti === 'function') {
      (window as any).confetti({
        particleCount: 50,
        spread: 60,
        origin: { y: 0.6 },
      });
    }

    // Banner overlay animation
    const banner = document.getElementById('overlay-level-up');
    const sub = document.getElementById('levelup-subtext');
    if (banner) {
      if (sub) sub.textContent = `${this.state.currentLevelConfig.name} • Speed Increased!`;
      banner.classList.remove('opacity-0', 'scale-90');
      banner.classList.add('opacity-100', 'scale-100');

      setTimeout(() => {
        banner.classList.remove('opacity-100', 'scale-100');
        banner.classList.add('opacity-0', 'scale-90');
      }, 1600);
    }
  }

  private handleGameOver(reason: string): void {
    this.state.setStatus('GAME_OVER');
    this.loop.stop();
    this.audio.playGameOver();

    // Flash arena border
    const flash = document.getElementById('arena-flash-effect');
    if (flash) {
      flash.className = 'pointer-events-none absolute inset-0 bg-rose-600/30 opacity-100 transition-opacity duration-500';
      setTimeout(() => {
        flash.className = 'pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-500';
      }, 500);
    }

    // Update Game Over Modal
    const modal = document.getElementById('overlay-game-over');
    const goCause = document.getElementById('gameover-cause');
    const goScore = document.getElementById('go-score');
    const goLevel = document.getElementById('go-level');
    const goLen = document.getElementById('go-length');
    const goRecord = document.getElementById('go-record-msg');

    if (goCause) goCause.textContent = reason;
    if (goScore) goScore.textContent = String(this.state.score);
    if (goLevel) goLevel.textContent = String(this.state.level);
    if (goLen) goLen.textContent = String(this.snake.length);

    if (goRecord) {
      const isNewRecord = this.state.score >= this.state.highScore && this.state.score > 0;
      goRecord.classList.toggle('hidden', !isNewRecord);
    }

    if (modal) modal.classList.remove('hidden');

    // Save final state to Excel database
    void this.session.endSession(this.state.score, this.state.level, this.snake.length, false).then(() => {
      this.refreshLeaderboardUI();
    });
  }

  private render(interpolation: number): void {
    this.renderer.render(
      this.snake,
      this.ballManager,
      this.particles,
      this.state,
      interpolation,
    );
  }

  private startAutoCheckpoint(): void {
    if (this.checkpointTimer) clearInterval(this.checkpointTimer);

    this.checkpointTimer = window.setInterval(() => {
      if (this.state.status === 'PLAYING') {
        void this.triggerCheckpoint();
      }
    }, GAME_CONFIG.checkpointIntervalMs);
  }

  private async triggerCheckpoint(): Promise<void> {
    const syncStatus = document.getElementById('hud-sync-status');
    const saveTime = document.getElementById('hud-save-time');

    if (syncStatus) {
      syncStatus.textContent = 'Saving...';
      syncStatus.className = 'text-amber-400 font-semibold';
    }

    const updated = await this.session.saveCheckpoint(
      this.state.score,
      this.state.level,
      this.snake.length,
      'IN_PROGRESS',
    );

    if (syncStatus) {
      syncStatus.textContent = updated ? 'Synced' : 'Retry';
      syncStatus.className = updated ? 'text-emerald-400 font-semibold' : 'text-rose-400 font-semibold';
    }
    if (saveTime && updated) {
      saveTime.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
  }

  private async refreshLeaderboardUI(): Promise<void> {
    const list = await this.session.fetchLeaderboard();
    const container = document.getElementById('leaderboard-container');
    if (!container || list.length === 0) return;

    container.innerHTML = list.map((lb: any) => `
      <div class="flex items-center justify-between p-2.5 rounded-xl border border-white/5 bg-slate-950/60 text-xs">
        <div class="flex items-center gap-2.5 min-w-0">
          <span class="w-5 h-5 rounded-full font-bold text-[10px] flex items-center justify-center ${
            lb.rank === 1 ? 'bg-amber-400 text-slate-950' : lb.rank === 2 ? 'bg-slate-300 text-slate-950' : 'bg-slate-800 text-slate-300'
          }">
            ${lb.rank}
          </span>
          <span class="font-semibold text-slate-200 truncate">${this.escapeHtml(lb.username)}</span>
        </div>
        <div class="text-right font-mono">
          <span class="font-bold text-amber-300">${lb.highScore.toLocaleString()}</span>
          <span class="text-[10px] text-slate-500 block">Lv ${lb.currentLevel}</span>
        </div>
      </div>
    `).join('');
  }

  private escapeHtml(str: string): string {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  private toggleDebug(): void {
    this.isDebugVisible = !this.isDebugVisible;
    const panel = document.getElementById('debug-panel');
    if (panel) {
      panel.classList.toggle('hidden', !this.isDebugVisible);
    }
  }

  private updateDebugStats(): void {
    if (!this.isDebugVisible) return;

    const fpsEl = document.getElementById('dbg-fps');
    const stateEl = document.getElementById('dbg-state');
    const headEl = document.getElementById('dbg-head');
    const ballEl = document.getElementById('dbg-ball');
    const dirEl = document.getElementById('dbg-dir');
    const spdEl = document.getElementById('dbg-speed');

    if (fpsEl) fpsEl.textContent = String(this.loop.fps);
    if (stateEl) stateEl.textContent = this.state.status;
    if (headEl) headEl.textContent = `${this.snake.head.x},${this.snake.head.y}`;
    if (ballEl) ballEl.textContent = `${this.ballManager.ball.x},${this.ballManager.ball.y}`;
    if (dirEl) dirEl.textContent = this.snake.currentDirection;
    if (spdEl) spdEl.textContent = `${this.state.currentLevelConfig.stepIntervalMs}ms`;
  }
}
