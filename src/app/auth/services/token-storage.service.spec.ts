import { TestBed } from '@angular/core/testing';
import { TokenStorageService } from './token-storage.service';

describe('TokenStorageService', () => {
  const TOKEN_KEY = 'vendedores_token';
  let service: TokenStorageService;

  beforeEach(() => {
    localStorage.removeItem(TOKEN_KEY);
    TestBed.configureTestingModule({});
    service = TestBed.inject(TokenStorageService);
  });

  afterEach(() => {
    try {
      localStorage.removeItem(TOKEN_KEY);
    } catch {
      // Ignore if test spied on Storage.prototype.removeItem to throw
    }
  });

  it('should store, retrieve, check, and clear token using vendedores_token key', () => {
    expect(service.getToken()).toBeNull();
    expect(service.hasToken()).toBeFalse();

    service.setToken('jwt-token-123');

    expect(localStorage.getItem(TOKEN_KEY)).toBe('jwt-token-123');
    expect(service.getToken()).toBe('jwt-token-123');
    expect(service.hasToken()).toBeTrue();

    service.clearToken();

    expect(localStorage.getItem(TOKEN_KEY)).toBeNull();
    expect(service.getToken()).toBeNull();
    expect(service.hasToken()).toBeFalse();
  });

  it('should return null and false safely when localStorage.getItem throws', () => {
    spyOn(Storage.prototype, 'getItem').and.throwError('Storage unavailable');

    expect(service.getToken()).toBeNull();
    expect(service.hasToken()).toBeFalse();
  });

  it('should not throw when localStorage.setItem throws', () => {
    spyOn(Storage.prototype, 'setItem').and.throwError('Quota exceeded');

    expect(() => service.setToken('jwt-token-123')).not.toThrow();
  });

  it('should not throw when localStorage.removeItem throws', () => {
    spyOn(Storage.prototype, 'removeItem').and.throwError('Storage locked');

    expect(() => service.clearToken()).not.toThrow();
  });
});
