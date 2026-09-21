export class GameLoop {
  private rafId: number | null = null;
  private lastTime: number = 0;
  private accumulator: number = 0;
  private isRunning: boolean = false;

  private onUpdate: (dt: number) => void;
  private onRender: (interpolation: number) => void;
  private getStepInterval: () => number;

  public fps: number = 60;
  private frameCount: number = 0;
  private lastFpsUpdate: number = 0;

  constructor(
    onUpdate: (dt: number) => void,
    onRender: (interpolation: number) => void,
    getStepInterval: () => number,
  ) {
    this.onUpdate = onUpdate;
    this.onRender = onRender;
    this.getStepInterval = getStepInterval;
  }

  public start(): void {
    if (this.isRunning) return;
    this.isRunning = true;
    this.lastTime = performance.now();
    this.accumulator = 0;
    this.lastFpsUpdate = this.lastTime;
    this.frameCount = 0;
    this.rafId = requestAnimationFrame(this.loop.bind(this));
  }

  public stop(): void {
    this.isRunning = false;
    if (this.rafId !== null) {
      cancelAnimationFrame(this.rafId);
      this.rafId = null;
    }
  }

  private loop(currentTime: number): void {
    if (!this.isRunning) return;

    let dt = (currentTime - this.lastTime) / 1000;
    if (dt > 0.25) dt = 0.25; // Clamp delta to prevent spiral of death on tab unfocus
    this.lastTime = currentTime;

    // FPS calculation
    this.frameCount++;
    if (currentTime - this.lastFpsUpdate >= 1000) {
      this.fps = Math.round((this.frameCount * 1000) / (currentTime - this.lastFpsUpdate));
      this.frameCount = 0;
      this.lastFpsUpdate = currentTime;
    }

    const stepInterval = this.getStepInterval() / 1000;
    this.accumulator += dt;

    while (this.accumulator >= stepInterval) {
      this.onUpdate(stepInterval);
      this.accumulator -= stepInterval;
    }

    const interpolation = this.accumulator / stepInterval;
    this.onRender(interpolation);

    this.rafId = requestAnimationFrame(this.loop.bind(this));
  }
}
