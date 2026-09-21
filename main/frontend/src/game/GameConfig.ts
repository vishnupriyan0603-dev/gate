import { LevelConfig } from './types';

export const GAME_CONFIG = {
  // Logical Grid Coordinates
  gridSize: 24, // 24 x 24 tiles
  initialLength: 3,
  initialSpeedMs: 130,

  // Levels balancing
  levels: [
    {
      level: 1,
      minScore: 0,
      maxScore: 99,
      stepIntervalMs: 130,
      ballPoints: 10,
      bonusMultiplier: 1,
      name: 'Novice Python',
    },
    {
      level: 2,
      minScore: 100,
      maxScore: 249,
      stepIntervalMs: 110,
      ballPoints: 15,
      bonusMultiplier: 1.25,
      name: 'Viper Strike',
    },
    {
      level: 3,
      minScore: 250,
      maxScore: 499,
      stepIntervalMs: 95,
      ballPoints: 20,
      bonusMultiplier: 1.5,
      name: 'Cobrastar',
    },
    {
      level: 4,
      minScore: 500,
      maxScore: 999,
      stepIntervalMs: 80,
      ballPoints: 30,
      bonusMultiplier: 2,
      name: 'Titan Hydra',
    },
    {
      level: 5,
      minScore: 1000,
      maxScore: Infinity,
      stepIntervalMs: 68,
      ballPoints: 50,
      bonusMultiplier: 2.5,
      name: 'Ouroboros God',
    },
  ] as LevelConfig[],

  // Aesthetics & Palette
  colors: {
    bgDark: '#070a13',
    gridLine: 'rgba(255, 255, 255, 0.035)',
    gridGlow: 'rgba(124, 58, 237, 0.08)',
    snakeHead: '#8b5cf6', // Violet
    snakeHeadGlow: '#a78bfa',
    snakeBodyStart: '#7c3aed',
    snakeBodyEnd: '#06b6d4', // Cyan
    snakeEye: '#ffffff',
    snakeEyePupil: '#1e1b4b',
    ballMain: '#f59e0b', // Amber/gold
    ballGlow: '#fbbf24',
    ballAura: 'rgba(245, 158, 11, 0.25)',
  },

  // Auto-Save interval
  checkpointIntervalMs: 15000, // Every 15 seconds
};
