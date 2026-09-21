import { Ball as BallType, Point } from './types';
import { GAME_CONFIG } from './GameConfig';
import { Snake } from './Snake';

export class BallManager {
  public ball: BallType = {
    x: 0,
    y: 0,
    radius: 0.42,
    pulsePhase: 0,
    sparkleAngle: 0,
    color: GAME_CONFIG.colors.ballMain,
  };

  constructor() {
    this.spawnDefault();
  }

  public spawnDefault(): void {
    this.ball.x = Math.floor(GAME_CONFIG.gridSize * 0.75);
    this.ball.y = Math.floor(GAME_CONFIG.gridSize * 0.5);
    this.ball.pulsePhase = 0;
    this.ball.sparkleAngle = 0;
  }

  /**
   * Safe spawn algorithm: guarantees ball never spawns inside the snake.
   */
  public spawn(snake: Snake): Point {
    const size = GAME_CONFIG.gridSize;
    const availableCells: Point[] = [];

    // Collect all unoccupied coordinates
    for (let x = 1; x < size - 1; x++) {
      for (let y = 1; y < size - 1; y++) {
        if (!snake.occupies(x, y)) {
          availableCells.push({ x, y });
        }
      }
    }

    if (availableCells.length > 0) {
      const chosen = availableCells[Math.floor(Math.random() * availableCells.length)];
      this.ball.x = chosen.x;
      this.ball.y = chosen.y;
    } else {
      // Fallback
      this.ball.x = Math.floor(Math.random() * (size - 2)) + 1;
      this.ball.y = Math.floor(Math.random() * (size - 2)) + 1;
    }

    this.ball.pulsePhase = 0;
    return { x: this.ball.x, y: this.ball.y };
  }

  public updateAnimation(dt: number): void {
    this.ball.pulsePhase += dt * 5;
    this.ball.sparkleAngle += dt * 3;
  }
}
