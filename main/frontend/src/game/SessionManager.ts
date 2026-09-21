import { PlayerData } from './types';

export class SessionManager {
  private baseUrl: string;
  private currentSessionId: string = '';
  private currentUsername: string = '';
  private lastSavedScore: number = 0;
  private isSaving: boolean = false;

  constructor() {
    this.baseUrl = (window as unknown as { GATE_BASE?: string }).GATE_BASE || '';
    try {
      this.currentUsername = localStorage.getItem('snake_player_username') || '';
      this.currentSessionId = localStorage.getItem('snake_session_id') || '';
    } catch {
      /* ignore */
    }
  }

  public getUsername(): string {
    return this.currentUsername;
  }

  public getSessionId(): string {
    return this.currentSessionId;
  }

  public async startSession(username: string): Promise<{ isNew: boolean; canResume: boolean; player: PlayerData }> {
    const res = await fetch(`${this.baseUrl}/game/session/start`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username }),
    });

    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      throw new Error(err.message || 'Failed to start session');
    }

    const data = await res.json();
    this.currentUsername = username;
    this.currentSessionId = data.player.Session_ID;
    this.lastSavedScore = data.player.Current_Score || 0;

    try {
      localStorage.setItem('snake_player_username', username);
      localStorage.setItem('snake_session_id', this.currentSessionId);
    } catch {
      /* ignore */
    }

    return data;
  }

  public async saveCheckpoint(score: number, level: number, snakeLength: number, status = 'IN_PROGRESS'): Promise<PlayerData | null> {
    if (!this.currentSessionId || this.isSaving) return null;

    this.isSaving = true;
    try {
      const res = await fetch(`${this.baseUrl}/game/session/checkpoint`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          sessionId: this.currentSessionId,
          score,
          level,
          snakeLength,
          status,
        }),
      });

      if (!res.ok) return null;
      const data = await res.json();
      this.lastSavedScore = score;
      return data.player;
    } catch {
      return null;
    } finally {
      this.isSaving = false;
    }
  }

  public async endSession(score: number, level: number, snakeLength: number, completed = false): Promise<PlayerData | null> {
    if (!this.currentSessionId) return null;

    try {
      const res = await fetch(`${this.baseUrl}/game/session/end`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          sessionId: this.currentSessionId,
          score,
          level,
          snakeLength,
          completed,
        }),
      });

      if (!res.ok) return null;
      const data = await res.json();
      return data.player;
    } catch {
      return null;
    }
  }

  public async resetNewGame(): Promise<PlayerData | null> {
    if (!this.currentSessionId) return null;

    try {
      const res = await fetch(`${this.baseUrl}/game/session/new`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ sessionId: this.currentSessionId }),
      });

      if (!res.ok) return null;
      const data = await res.json();
      return data.player;
    } catch {
      return null;
    }
  }

  public async fetchLeaderboard(): Promise<any[]> {
    try {
      const res = await fetch(`${this.baseUrl}/game/leaderboard`);
      if (!res.ok) return [];
      const data = await res.json();
      return data.leaderboard || [];
    } catch {
      return [];
    }
  }
}
