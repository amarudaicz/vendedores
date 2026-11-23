import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class TokenStorageService {
  private readonly TOKEN_KEY = 'vendedores_token';

  getToken(): string | null {
    if (typeof localStorage === 'undefined') {
      return null;
    }

    try {
      return localStorage.getItem(this.TOKEN_KEY);
    } catch {
      return null;
    }
  }

  setToken(token: string): void {
    if (typeof localStorage === 'undefined') {
      return;
    }

    try {
      localStorage.setItem(this.TOKEN_KEY, token);
    } catch {
      // Ignore storage access errors in restricted browser contexts
    }
  }

  clearToken(): void {
    if (typeof localStorage === 'undefined') {
      return;
    }

    try {
      localStorage.removeItem(this.TOKEN_KEY);
    } catch {
      // Ignore storage access errors in restricted browser contexts
    }
  }

  hasToken(): boolean {
    return Boolean(this.getToken());
  }
}
