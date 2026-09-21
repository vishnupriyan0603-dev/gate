import { Snake } from './Snake';
import { BallManager } from './Ball';
import { ParticleSystem } from './ParticleSystem';
import { GameState } from './GameState';
import { GAME_CONFIG } from './GameConfig';

export class CanvasRenderer {
  private canvas: HTMLCanvasElement;
  private ctx: CanvasRenderingContext2D;
  private tileSize = 20;

  constructor(canvas: HTMLCanvasElement) {
    this.canvas = canvas;
    const context = canvas.getContext('2d', { alpha: false });
    if (!context) {
      throw new Error('Canvas 2D context could not be created');
    }
    this.ctx = context;
    this.handleResize();
  }

  public handleResize(): void {
    const rect = this.canvas.getBoundingClientRect();
    const dpr = window.devicePixelRatio || 1;
    const displayWidth = Math.round(rect.width || 600);
    const displayHeight = Math.round(rect.height || 600);

    // Set internal buffer resolution
    this.canvas.width = displayWidth * dpr;
    this.canvas.height = displayHeight * dpr;

    this.ctx.resetTransform();
    this.ctx.scale(dpr, dpr);

    this.tileSize = displayWidth / GAME_CONFIG.gridSize;
  }

  public render(
    snake: Snake,
    ballManager: BallManager,
    particles: ParticleSystem,
    state: GameState,
    interpolation = 1.0,
  ): void {
    const ctx = this.ctx;
    const width = this.canvas.width / (window.devicePixelRatio || 1);
    const height = this.canvas.height / (window.devicePixelRatio || 1);
    const tSize = this.tileSize;

    // 1. Clear background
    ctx.fillStyle = GAME_CONFIG.colors.bgDark;
    ctx.fillRect(0, 0, width, height);

    // 2. Draw subtle arcade grid
    ctx.lineWidth = 1;
    ctx.strokeStyle = GAME_CONFIG.colors.gridLine;

    const gridSize = GAME_CONFIG.gridSize;
    for (let i = 0; i <= gridSize; i++) {
      const pos = i * tSize;
      ctx.beginPath();
      ctx.moveTo(pos, 0);
      ctx.lineTo(pos, height);
      ctx.stroke();

      ctx.beginPath();
      ctx.moveTo(0, pos);
      ctx.lineTo(width, pos);
      ctx.stroke();
    }

    // Subtle arena vignette
    const radial = ctx.createRadialGradient(
      width / 2,
      height / 2,
      width * 0.2,
      width / 2,
      height / 2,
      width * 0.75,
    );
    radial.addColorStop(0, 'rgba(124, 58, 237, 0.04)');
    radial.addColorStop(1, 'rgba(0, 0, 0, 0.45)');
    ctx.fillStyle = radial;
    ctx.fillRect(0, 0, width, height);

    // 3. Render Food / Ball
    this.renderBall(ballManager);

    // 4. Render Snake
    this.renderSnake(snake, state, interpolation);

    // 5. Render Particles & Shockwaves
    particles.render(ctx);
  }

  private renderBall(ballManager: BallManager): void {
    const ctx = this.ctx;
    const tSize = this.tileSize;
    const ball = ballManager.ball;

    const centerX = (ball.x + 0.5) * tSize;
    const centerY = (ball.y + 0.5) * tSize;

    // Pulsing radius
    const pulse = Math.sin(ball.pulsePhase) * 0.12;
    const baseRadius = tSize * (ball.radius + pulse);

    ctx.save();

    // Outer aura glow
    ctx.beginPath();
    ctx.arc(centerX, centerY, baseRadius * 1.8, 0, Math.PI * 2);
    ctx.fillStyle = GAME_CONFIG.colors.ballAura;
    ctx.shadowColor = GAME_CONFIG.colors.ballGlow;
    ctx.shadowBlur = 16;
    ctx.fill();

    // Main orb
    const grad = ctx.createRadialGradient(
      centerX - baseRadius * 0.3,
      centerY - baseRadius * 0.3,
      baseRadius * 0.1,
      centerX,
      centerY,
      baseRadius,
    );
    grad.addColorStop(0, '#ffffff');
    grad.addColorStop(0.35, '#fde047');
    grad.addColorStop(0.8, '#f59e0b');
    grad.addColorStop(1, '#d97706');

    ctx.beginPath();
    ctx.arc(centerX, centerY, baseRadius, 0, Math.PI * 2);
    ctx.fillStyle = grad;
    ctx.shadowColor = '#f59e0b';
    ctx.shadowBlur = 12;
    ctx.fill();

    // Orbital sparkles
    const orbitRadius = baseRadius * 1.5;
    for (let i = 0; i < 3; i++) {
      const angle = ball.sparkleAngle + (i * Math.PI * 2) / 3;
      const sx = centerX + Math.cos(angle) * orbitRadius;
      const sy = centerY + Math.sin(angle) * orbitRadius;

      ctx.beginPath();
      ctx.arc(sx, sy, 2, 0, Math.PI * 2);
      ctx.fillStyle = '#ffffff';
      ctx.shadowBlur = 4;
      ctx.shadowColor = '#ffffff';
      ctx.fill();
    }

    ctx.restore();
  }

  private renderSnake(snake: Snake, state: GameState, _interpolation: number): void {
    const ctx = this.ctx;
    const tSize = this.tileSize;
    const segs = snake.segments;

    if (segs.length === 0) return;

    ctx.save();

    // Render body segments from tail to neck
    for (let i = segs.length - 1; i >= 1; i--) {
      const seg = segs[i];
      const progress = i / Math.max(1, segs.length - 1); // 0 at neck, 1 at tail

      const x = seg.x * tSize + 1.5;
      const y = seg.y * tSize + 1.5;
      const size = tSize - 3;
      const radius = Math.max(3, size * 0.35);

      // Color lerp from violet to cyan
      const r = Math.round(124 * (1 - progress) + 6 * progress);
      const g = Math.round(58 * (1 - progress) + 182 * progress);
      const b = Math.round(237 * (1 - progress) + 212 * progress);

      ctx.fillStyle = `rgb(${r}, ${g}, ${b})`;
      ctx.shadowColor = `rgba(${r}, ${g}, ${b}, 0.4)`;
      ctx.shadowBlur = 4;

      this.roundRect(ctx, x, y, size, size, radius);
      ctx.fill();

      // Subtle inner highlight
      ctx.fillStyle = 'rgba(255, 255, 255, 0.12)';
      this.roundRect(ctx, x + 2, y + 2, size - 4, size - 4, radius * 0.8);
      ctx.fill();
    }

    // Render Head with extra glow and eyes
    const head = snake.head;
    const hx = head.x * tSize + 1;
    const hy = head.y * tSize + 1;
    const hSize = tSize - 2;

    ctx.fillStyle = GAME_CONFIG.colors.snakeHead;
    ctx.shadowColor = GAME_CONFIG.colors.snakeHeadGlow;
    ctx.shadowBlur = 14;

    this.roundRect(ctx, hx, hy, hSize, hSize, hSize * 0.45);
    ctx.fill();

    // Eyes
    this.renderSnakeEyes(ctx, head.x, head.y, snake.currentDirection, tSize);

    ctx.restore();
  }

  private renderSnakeEyes(
    ctx: CanvasRenderingContext2D,
    gridX: number,
    gridY: number,
    dir: string,
    tSize: number,
  ): void {
    const cx = (gridX + 0.5) * tSize;
    const cy = (gridY + 0.5) * tSize;
    const eyeOffset = tSize * 0.22;
    const eyeRadius = tSize * 0.13;
    const pupilRadius = tSize * 0.07;

    let e1x = 0;
    let e1y = 0;
    let e2x = 0;
    let e2y = 0;
    let pupDx = 0;
    let pupDy = 0;

    switch (dir) {
      case 'UP':
        e1x = cx - eyeOffset;
        e1y = cy - eyeOffset * 0.5;
        e2x = cx + eyeOffset;
        e2y = cy - eyeOffset * 0.5;
        pupDy = -eyeRadius * 0.4;
        break;
      case 'DOWN':
        e1x = cx - eyeOffset;
        e1y = cy + eyeOffset * 0.5;
        e2x = cx + eyeOffset;
        e2y = cy + eyeOffset * 0.5;
        pupDy = eyeRadius * 0.4;
        break;
      case 'LEFT':
        e1x = cx - eyeOffset * 0.5;
        e1y = cy - eyeOffset;
        e2x = cx - eyeOffset * 0.5;
        e2y = cy + eyeOffset;
        pupDx = -eyeRadius * 0.4;
        break;
      case 'RIGHT':
      default:
        e1x = cx + eyeOffset * 0.5;
        e1y = cy - eyeOffset;
        e2x = cx + eyeOffset * 0.5;
        e2y = cy + eyeOffset;
        pupDx = eyeRadius * 0.4;
        break;
    }

    // Eye Whites
    ctx.fillStyle = GAME_CONFIG.colors.snakeEye;
    ctx.shadowBlur = 0;

    ctx.beginPath();
    ctx.arc(e1x, e1y, eyeRadius, 0, Math.PI * 2);
    ctx.arc(e2x, e2y, eyeRadius, 0, Math.PI * 2);
    ctx.fill();

    // Eye Pupils
    ctx.fillStyle = GAME_CONFIG.colors.snakeEyePupil;
    ctx.beginPath();
    ctx.arc(e1x + pupDx, e1y + pupDy, pupilRadius, 0, Math.PI * 2);
    ctx.arc(e2x + pupDx, e2y + pupDy, pupilRadius, 0, Math.PI * 2);
    ctx.fill();
  }

  private roundRect(
    ctx: CanvasRenderingContext2D,
    x: number,
    y: number,
    w: number,
    h: number,
    r: number,
  ): void {
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.lineTo(x + w - r, y);
    ctx.quadraticCurveTo(x + w, y, x + w, y + r);
    ctx.lineTo(x + w, y + h - r);
    ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
    ctx.lineTo(x + r, y + h);
    ctx.quadraticCurveTo(x, y + h, x, y + h - r);
    ctx.lineTo(x, y + r);
    ctx.quadraticCurveTo(x, y, x + r, y);
    ctx.closePath();
  }
}
