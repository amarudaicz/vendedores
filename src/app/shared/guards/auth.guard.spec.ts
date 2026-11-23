import { provideHttpClient } from '@angular/common/http';
import {
  HttpTestingController,
  provideHttpClientTesting,
} from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import {
  ActivatedRouteSnapshot,
  GuardResult,
  MaybeAsync,
  Router,
  RouterStateSnapshot,
  UrlTree,
  provideRouter,
} from '@angular/router';
import { firstValueFrom, isObservable } from 'rxjs';
import { environment } from '@environments/environment';
import { AuthService } from '../../auth/services/auth.service';
import { TokenStorageService } from '../../auth/services/token-storage.service';
import { Seller } from '../../interfaces/seller';
import { authGuard } from './auth.guard';

describe('authGuard', () => {
  let authService: AuthService;
  let tokenStorage: TokenStorageService;
  let router: Router;
  let httpMock: HttpTestingController;

  const mockRoute = {} as ActivatedRouteSnapshot;
  const mockState = { url: '/orders?page=2' } as RouterStateSnapshot;
  const mockSeller: Seller = {
    id: 7,
    name: 'Ana Vendedora',
    email: 'ana@example.com',
    code: 202,
    deleted: false,
    createdAt: '2026-01-01T00:00:00Z',
  };

  beforeEach(() => {
    localStorage.removeItem('vendedores_token');

    TestBed.configureTestingModule({
      providers: [
        provideRouter([]),
        provideHttpClient(),
        provideHttpClientTesting(),
      ],
    });

    authService = TestBed.inject(AuthService);
    tokenStorage = TestBed.inject(TokenStorageService);
    router = TestBed.inject(Router);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    httpMock.verify();
    localStorage.removeItem('vendedores_token');
  });

  async function resolveGuardResult(
    result: MaybeAsync<GuardResult>,
  ): Promise<GuardResult> {
    if (isObservable(result)) {
      return await firstValueFrom(result);
    }
    return await result;
  }

  it('should return UrlTree to /login?returnUrl=... with zero HTTP calls when no token exists', async () => {
    const isLoggedSpy = spyOn(authService, 'isLogged').and.callThrough();

    const guardOutput = TestBed.runInInjectionContext(() =>
      authGuard(mockRoute, mockState),
    );
    const result = await resolveGuardResult(guardOutput);

    expect(isLoggedSpy).not.toHaveBeenCalled();
    httpMock.expectNone(() => true);
    expect(result instanceof UrlTree).toBeTrue();
    expect(router.serializeUrl(result as UrlTree)).toBe(
      '/login?returnUrl=%2Forders%3Fpage%3D2',
    );
  });

  it('should call GET auth/me, hydrate seller$, and return true when token exists and seller$ is null', async () => {
    tokenStorage.setToken('valid-token');
    let currentSeller = null as Seller | null;
    authService.getSeller().subscribe((seller) => {
      currentSeller = seller;
    });

    const guardOutput = TestBed.runInInjectionContext(() =>
      authGuard(mockRoute, mockState),
    );
    const resultPromise = resolveGuardResult(guardOutput);

    const req = httpMock.expectOne(`${environment.apiUrl}auth/me`);
    expect(req.request.method).toBe('GET');
    req.flush({ success: true, data: { seller: mockSeller } });

    const result = await resultPromise;
    expect(result).toBeTrue();
    expect(currentSeller).toEqual(mockSeller);
  });

  it('should clear token, reset seller$ to null, and return UrlTree to /login?returnUrl=... when GET auth/me fails', async () => {
    tokenStorage.setToken('expired-token');
    let currentSeller = mockSeller as Seller | null;
    authService.getSeller().subscribe((seller) => {
      currentSeller = seller;
    });

    const guardOutput = TestBed.runInInjectionContext(() =>
      authGuard(mockRoute, mockState),
    );
    const resultPromise = resolveGuardResult(guardOutput);

    const req = httpMock.expectOne(`${environment.apiUrl}auth/me`);
    req.flush(
      { message: 'Unauthorized' },
      { status: 401, statusText: 'Unauthorized' },
    );

    const result = await resultPromise;
    expect(tokenStorage.hasToken()).toBeFalse();
    expect(currentSeller).toBeNull();
    expect(result instanceof UrlTree).toBeTrue();
    expect(router.serializeUrl(result as UrlTree)).toBe(
      '/login?returnUrl=%2Forders%3Fpage%3D2',
    );
  });
});
