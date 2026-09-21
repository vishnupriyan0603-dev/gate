export type Direction = 'UP' | 'DOWN' | 'LEFT' | 'RIGHT';

export type GameStatus =
  | 'MENU'
  | 'READY'
  | 'PLAYING'
  | 'PAUSED'
  | 'LEVEL_UP'
  | 'GAME_OVER'
  | 'RESULT';

export interface Point {
  x: number;
  y: number;
}

export interface SnakeSegment extends Point {
  prevX?: number;
  prevY?: number;
}

export interface Ball extends Point {
  radius: number;
  pulsePhase: number;
  sparkleAngle: number;
  color: string;
}

export interface LevelConfig {
  level: number;
  minScore: number;
  maxScore: number;
  stepIntervalMs: number;
  ballPoints: number;
  bonusMultiplier: number;
  name: string;
}

export interface PlayerData {
  ID?: number | string;
  Username: string;
  Session_ID: string;
  Current_Level: number;
  Current_Score: number;
  High_Score: number;
  Snake_Length: number;
  Games_Played: number;
  Games_Completed: number;
  Last_Save_Point: number;
  Game_Status: string;
}

export interface Particle {
  x: number;
  y: number;
  vx: number;
  vy: number;
  life: number;
  maxLife: number;
  size: number;
  color: string;
  alpha: number;
}

export interface Shockwave {
  x: number;
  y: number;
  radius: number;
  maxRadius: number;
  color: string;
  alpha: number;
}
