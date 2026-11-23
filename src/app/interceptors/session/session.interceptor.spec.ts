import {
  HttpClient,
  HttpErrorResponse,
  provideHttpClient,
  withInterceptors,
} from '@angular/common/http';
import {
  HttpTestingController,
  provideHttpClientTesting,
} from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { Router } from '@angular/router';
import { AuthService } from '../../auth/services/auth.service';
import { TokenStorageService } from '../../auth/services/token-storage.service';
import AlertService from '../../shared/services/alert/alert.service';
import { SessionInterceptor } from './session.interceptor';

describe('SessionInterceptor', () => {
  let http: HttpClient;
  let httpMock: HttpTestingController;
  let alertServiceSpy: jasmine.SpyObj<AlertService>;
  let tokenStorageSpy: jasmine.SpyObj<TokenStorageService>;
  let authServiceSpy: jasmine.SpyObj<AuthService>;
  let routerMock: { url: string; navigate: jasmine.Spy };

  beforeEach(() => {
    alertServiceSpy = jasmine.createSpyObj<AlertService>('AlertService', [
      'showAlert',
    ]);
    tokenStorageSpy = jasmine.createSpyObj<TokenStorageService>(
      'TokenStorageService',
      ['clearToken'],
    );
    authServiceSpy = jasmine.createSpyObj<AuthService>('AuthService', [
      'nextSeller',
    ]);
    routerMock = {
      url: '/orders',
      navigate: jasmine.createSpy('navigate'),
    };

    TestBed.configureTestingModule({
      providers: [
        provideHttpClient(withInterceptors([SessionInterceptor])),
        provideHttpClientTesting(),
        { provide: AlertService, useValue: alertServiceSpy },
        { provide: TokenStorageService, useValue: tokenStorageSpy },
        { provide: AuthService, useValue: authServiceSpy },
        { provide: Router, useValue: routerMock },
      ],
    });

    http = TestBed.inject(HttpClient);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    httpMock.verify();
  });

  it('should clear token, reset seller, show info toast, and navigate to /login on 401 for non-login request', () => {
    let capturedError: HttpErrorResponse | undefined;

    http.get('/api/seller/orders').subscribe({
      error: (err: HttpErrorResponse) => {
        capturedError = err;
      },
    });

    const req = httpMock.expectOne('/api/seller/orders');
    req.flush({ message: 'Unauthorized' }, { status: 401, statusText: 'Unauthorized' });

    expect(capturedError?.status).toBe(401);
    expect(tokenStorageSpy.clearToken).toHaveBeenCalledTimes(1);
    expect(authServiceSpy.nextSeller).toHaveBeenCalledWith(null);
    expect(alertServiceSpy.showAlert).toHaveBeenCalledWith(
      'Sesión expirada, por favor inicie sesión nuevamente',
      'info',
    );
    expect(routerMock.navigate).toHaveBeenCalledWith(['/login']);
  });

  it('should skip session-expired redirect and toast on 401 for /auth/login request', () => {
    let capturedError: HttpErrorResponse | undefined;

    http.post('/api/seller/auth/login', {}).subscribe({
      error: (err: HttpErrorResponse) => {
        capturedError = err;
      },
    });

    const req = httpMock.expectOne('/api/seller/auth/login');
    req.flush({ message: 'Invalid credentials' }, { status: 401, statusText: 'Unauthorized' });

    expect(capturedError?.status).toBe(401);
    expect(tokenStorageSpy.clearToken).not.toHaveBeenCalled();
    expect(authServiceSpy.nextSeller).not.toHaveBeenCalled();
    expect(alertServiceSpy.showAlert).not.toHaveBeenCalled();
    expect(routerMock.navigate).not.toHaveBeenCalled();
  });
});
