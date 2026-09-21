import { Game } from './Game';

export function init(): void {
  const canvas = document.getElementById('game-canvas') as HTMLCanvasElement | null;
  if (!canvas) {
    return;
  }

  // Initialize Game Instance
  const game = new Game(canvas);
  (window as unknown as { __SNAKE_GAME__?: Game }).__SNAKE_GAME__ = game;
}
