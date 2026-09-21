import { GameStatus, LevelConfig, PlayerData } from './types';
import { GAME_CONFIG } from './GameConfig';

export type StateChangeCallback = (state: GameState) => void;

export class GameState {
  public status: GameStatus = 'MENU';
  public score: number = 0;
  public highScore: number = 0;
  public level: number = 1;
  public snakeLength: number = 3;
  public playerName: string = 'Guest';
  public sessionId: string = '';
  public lastSavePoint: number = 0;
  public gamesPlayed: number = 0;
  public gamesCompleted: number = 0;

  private listeners: StateChangeCallback[] = [];

  constructor() {
    this.reset();
  }

  public reset(): void {
    this.score = 0;
    this.level = 1;
    this.snakeLength = GAME_CONFIG.initialLength;
    this.status = 'READY';
    this.notify();
  }

  public get currentLevelConfig(): LevelConfig {
    const found = GAME_CONFIG.levels.find((l) => l.level === this.level);
    return found || GAME_CONFIG.levels[0];
  }

  public addScore(points: number): boolean {
    const prevLevel = this.level;
    this.score += Math.round(points * this.currentLevelConfig.bonusMultiplier);

    if (this.score > this.highScore) {
      this.highScore = this.score;
    }

    // Check level progression
    for (const lvl of GAME_CONFIG.levels) {
      if (this.score >= lvl.minScore && this.score <= lvl.maxScore) {
        this.level = lvl.level;
        break;
      }
    }

    const leveledUp = this.level > prevLevel;
    this.notify();
    return leveledUp;
  }

  public setStatus(newStatus: GameStatus): void {
    this.status = newStatus;
    this.notify();
  }

  public setPlayerData(data: Partial<PlayerData>): void {
    if (data.Username) this.playerName = data.Username;
    if (data.Session_ID) this.sessionId = data.Session_ID;
    if (typeof data.High_Score === 'number') this.highScore = data.High_Score;
    if (typeof data.Current_Score === 'number') this.score = data.Current_Score;
    if (typeof data.Current_Level === 'number') this.level = data.Current_Level;
    if (typeof data.Snake_Length === 'number') this.snakeLength = data.Snake_Length;
    if (typeof data.Games_Played === 'number') this.gamesPlayed = data.Games_Played;
    if (typeof data.Games_Completed === 'number') this.gamesCompleted = data.Games_Completed;
    this.notify();
  }

  public onStateChange(cb: StateChangeCallback): () => void {
    this.listeners.push(cb);
    return () => {
      this.listeners = this.listeners.filter((l) => l !== cb);
    };
  }

  private notify(): void {
    for (const listener of this.listeners) {
      listener(this);
    }
  }
}
