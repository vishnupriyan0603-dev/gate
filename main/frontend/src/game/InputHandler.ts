import { Direction } from './types';

export interface InputCallbacks {
  onDirection: (dir: Direction) => void;
  onPauseToggle: () => void;
  onRestart: () => void;
  onDebugToggle: () => void;
}

export class InputHandler {
  private callbacks: InputCallbacks;
  private keydownHandler: (e: KeyboardEvent) => void;
  private touchStartX = 0;
  private touchStartY = 0;
  private canvasElement: HTMLElement | null = null;
  private dpadButtons: HTMLElement[] = [];

  constructor(callbacks: InputCallbacks) {
    this.callbacks = callbacks;
    this.keydownHandler = this.handleKeyDown.bind(this);
    this.initKeyboard();
    this.initDpad();
  }

  public bindCanvas(canvas: HTMLElement): void {
    this.canvasElement = canvas;
    this.canvasElement.addEventListener('touchstart', this.handleTouchStart.bind(this), { passive: false });
    this.canvasElement.addEventListener('touchend', this.handleTouchEnd.bind(this), { passive: false });
  }

  private initKeyboard(): void {
    window.addEventListener('keydown', this.keydownHandler);
  }

  private initDpad(): void {
    const buttons = document.querySelectorAll<HTMLElement>('.dpad-btn');
    buttons.forEach((btn) => {
      this.dpadButtons.push(btn);
      const dir = btn.dataset.dir as Direction;
      if (dir) {
        const handler = (e: Event) => {
          e.preventDefault();
          this.callbacks.onDirection(dir);
        };
        btn.addEventListener('pointerdown', handler);
      }
    });
  }

  private handleKeyDown(e: KeyboardEvent): void {
    // Ignore input if user is typing in a text field
    const activeEl = document.activeElement;
    if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA')) {
      return;
    }

    switch (e.code) {
      case 'ArrowUp':
      case 'KeyW':
        e.preventDefault();
        this.callbacks.onDirection('UP');
        break;
      case 'ArrowDown':
      case 'KeyS':
        e.preventDefault();
        this.callbacks.onDirection('DOWN');
        break;
      case 'ArrowLeft':
      case 'KeyA':
        e.preventDefault();
        this.callbacks.onDirection('LEFT');
        break;
      case 'ArrowRight':
      case 'KeyD':
        e.preventDefault();
        this.callbacks.onDirection('RIGHT');
        break;
      case 'Space':
        e.preventDefault();
        this.callbacks.onPauseToggle();
        break;
      case 'KeyR':
        e.preventDefault();
        this.callbacks.onRestart();
        break;
      case 'Backquote':
        e.preventDefault();
        this.callbacks.onDebugToggle();
        break;
    }
  }

  private handleTouchStart(e: TouchEvent): void {
    e.preventDefault();
    if (e.touches.length > 0) {
      this.touchStartX = e.touches[0].clientX;
      this.touchStartY = e.touches[0].clientY;
    }
  }

  private handleTouchEnd(e: TouchEvent): void {
    e.preventDefault();
    if (e.changedTouches.length === 0) return;

    const endX = e.changedTouches[0].clientX;
    const endY = e.changedTouches[0].clientY;

    const dx = endX - this.touchStartX;
    const dy = endY - this.touchStartY;

    const minSwipeDistance = 25;

    if (Math.abs(dx) > Math.abs(dy)) {
      // Horizontal swipe
      if (Math.abs(dx) > minSwipeDistance) {
        this.callbacks.onDirection(dx > 0 ? 'RIGHT' : 'LEFT');
      }
    } else {
      // Vertical swipe
      if (Math.abs(dy) > minSwipeDistance) {
        this.callbacks.onDirection(dy > 0 ? 'DOWN' : 'UP');
      }
    }
  }

  public destroy(): void {
    window.removeEventListener('keydown', this.keydownHandler);
  }
}
