import { Direction, Point, SnakeSegment } from './types';
import { GAME_CONFIG } from './GameConfig';

export class Snake {
  public segments: SnakeSegment[] = [];
  public currentDirection: Direction = 'RIGHT';
  private directionQueue: Direction[] = [];
  public pendingGrowth: number = 0;

  constructor() {
    this.reset();
  }

  public reset(): void {
    this.currentDirection = 'RIGHT';
    this.directionQueue = [];
    this.pendingGrowth = 0;

    const startX = Math.floor(GAME_CONFIG.gridSize / 3);
    const startY = Math.floor(GAME_CONFIG.gridSize / 2);

    this.segments = [
      { x: startX, y: startY, prevX: startX, prevY: startY },
      { x: startX - 1, y: startY, prevX: startX - 1, prevY: startY },
      { x: startX - 2, y: startY, prevX: startX - 2, prevY: startY },
    ];
  }

  public get head(): SnakeSegment {
    return this.segments[0];
  }

  public get length(): number {
    return this.segments.length;
  }

  public queueDirection(dir: Direction): void {
    const lastQueued = this.directionQueue.length > 0
      ? this.directionQueue[this.directionQueue.length - 1]
      : this.currentDirection;

    // Prevent immediate 180-degree reversal
    if (this.isOpposite(lastQueued, dir) || lastQueued === dir) {
      return;
    }

    // Allow maximum 2 queued directions for crisp responsiveness
    if (this.directionQueue.length < 2) {
      this.directionQueue.push(dir);
    }
  }

  private isOpposite(d1: Direction, d2: Direction): boolean {
    return (
      (d1 === 'UP' && d2 === 'DOWN') ||
      (d1 === 'DOWN' && d2 === 'UP') ||
      (d1 === 'LEFT' && d2 === 'RIGHT') ||
      (d1 === 'RIGHT' && d2 === 'LEFT')
    );
  }

  public move(): Point {
    if (this.directionQueue.length > 0) {
      this.currentDirection = this.directionQueue.shift()!;
    }

    // Calculate next head position
    const currentHead = this.head;
    let nextX = currentHead.x;
    let nextY = currentHead.y;

    switch (this.currentDirection) {
      case 'UP':
        nextY -= 1;
        break;
      case 'DOWN':
        nextY += 1;
        break;
      case 'LEFT':
        nextX -= 1;
        break;
      case 'RIGHT':
        nextX += 1;
        break;
    }

    // Save previous positions for smooth animation
    for (const seg of this.segments) {
      seg.prevX = seg.x;
      seg.prevY = seg.y;
    }

    // Create new head
    const newHead: SnakeSegment = {
      x: nextX,
      y: nextY,
      prevX: currentHead.x,
      prevY: currentHead.y,
    };

    this.segments.unshift(newHead);

    if (this.pendingGrowth > 0) {
      this.pendingGrowth--;
    } else {
      this.segments.pop();
    }

    return { x: nextX, y: nextY };
  }

  public grow(amount = 1): void {
    this.pendingGrowth += amount;
  }

  public occupies(x: number, y: number, ignoreHead = false): boolean {
    const startIdx = ignoreHead ? 1 : 0;
    for (let i = startIdx; i < this.segments.length; i++) {
      if (this.segments[i].x === x && this.segments[i].y === y) {
        return true;
      }
    }
    return false;
  }

  public checkSelfCollision(): boolean {
    const head = this.head;
    for (let i = 1; i < this.segments.length; i++) {
      if (this.segments[i].x === head.x && this.segments[i].y === head.y) {
        return true;
      }
    }
    return false;
  }
}
