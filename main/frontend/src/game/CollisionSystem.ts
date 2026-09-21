import { Snake } from './Snake';
import { BallManager } from './Ball';
import { GAME_CONFIG } from './GameConfig';

export class CollisionSystem {
  public static checkWallCollision(snake: Snake): boolean {
    const head = snake.head;
    const size = GAME_CONFIG.gridSize;

    return head.x < 0 || head.x >= size || head.y < 0 || head.y >= size;
  }

  public static checkSelfCollision(snake: Snake): boolean {
    return snake.checkSelfCollision();
  }

  public static checkBallCollision(snake: Snake, ballManager: BallManager): boolean {
    const head = snake.head;
    const ball = ballManager.ball;

    return head.x === ball.x && head.y === ball.y;
  }
}
