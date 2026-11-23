import { TestBed } from '@angular/core/testing';
import {
  ActivatedRouteSnapshot,
  Router,
  RouterStateSnapshot,
  UrlTree,
  provideRouter,
} from '@angular/router';
import { TokenStorageService } from '../../../auth/services/token-storage.service';
import { loginGuard } from './login.guard';

describe('loginGuard', () => {
  let router: Router;
  let tokenStorageSpy: jasmine.SpyObj<TokenStorageService>;

  const mockRoute = {} as ActivatedRouteSnapshot;
  const mockState = { url: '/login' } as RouterStateSnapshot;

  beforeEach(() => {
    tokenStorageSpy = jasmine.createSpyObj<TokenStorageService>(
      'TokenStorageService',
      ['hasToken'],
    );

    TestBed.configureTestingModule({
      providers: [
        provideRouter([]),
        { provide: TokenStorageService, useValue: tokenStorageSpy },
      ],
    });

    router = TestBed.inject(Router);
  });

  it('should return UrlTree to / when token exists', () => {
    tokenStorageSpy.hasToken.and.returnValue(true);

    const result = TestBed.runInInjectionContext(() =>
      loginGuard(mockRoute, mockState),
    );

    expect(result instanceof UrlTree).toBeTrue();
    expect(router.serializeUrl(result as UrlTree)).toBe('/');
  });

  it('should return true when no token exists', () => {
    tokenStorageSpy.hasToken.and.returnValue(false);

    const result = TestBed.runInInjectionContext(() =>
      loginGuard(mockRoute, mockState),
    );

    expect(result).toBeTrue();
  });
});
