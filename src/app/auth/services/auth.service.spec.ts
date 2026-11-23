import { provideHttpClient } from '@angular/common/http';
import {
  HttpTestingController,
  provideHttpClientTesting,
} from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { environment } from '@environments/environment';
import { Seller } from '../../interfaces/seller';
import { AuthService } from './auth.service';
import { TokenStorageService } from './token-storage.service';

describe('AuthService', () => {
  let service: AuthService;
  let tokenStorage: TokenStorageService;
  let httpMock: HttpTestingController;

  const mockSeller: Seller = {
    id: 1,
    name: 'Juan Perez',
    email: 'juan@example.com',
    code: 101,
    deleted: false,
    createdAt: '2026-01-01T00:00:00Z',
    isAdmin: false,
  };

  beforeEach(() => {
    localStorage.removeItem('vendedores_token');

    TestBed.configureTestingModule({
      providers: [provideHttpClient(), provideHttpClientTesting()],
    });

    service = TestBed.inject(AuthService);
    tokenStorage = TestBed.inject(TokenStorageService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    httpMock.verify();
    localStorage.removeItem('vendedores_token');
  });

  it('logIn() should POST auth/login without withCredentials, store token, and update seller$', () => {
    let latestSeller = null as Seller | null;
    service.getSeller().subscribe((seller) => {
      latestSeller = seller;
    });

    service
      .logIn({ email: 'juan@example.com', password: 'secret' })
      .subscribe();

    const req = httpMock.expectOne(`${environment.apiUrl}auth/login`);
    expect(req.request.method).toBe('POST');
    expect(req.request.withCredentials).toBeFalse();
    expect(req.request.body).toEqual({
      email: 'juan@example.com',
      password: 'secret',
    });

    req.flush({
      success: true,
      data: { token: 'login-jwt', seller: mockSeller },
    });

    expect(tokenStorage.getToken()).toBe('login-jwt');
    expect(latestSeller).toEqual(mockSeller);
  });

  it('isLogged() should GET auth/me without withCredentials', () => {
    let responseSeller: Seller | undefined;

    service.isLogged().subscribe((res) => {
      responseSeller = res.data.seller;
    });

    const req = httpMock.expectOne(`${environment.apiUrl}auth/me`);
    expect(req.request.method).toBe('GET');
    expect(req.request.withCredentials).toBeFalse();

    req.flush({ success: true, data: { seller: mockSeller } });
    expect(responseSeller).toEqual(mockSeller);
  });

  it('refreshToken() should POST auth/refresh, rotate stored token, and update seller$', () => {
    tokenStorage.setToken('old-jwt');
    const updatedSeller: Seller = { ...mockSeller, name: 'Juan Updated' };
    let latestSeller = null as Seller | null;
    service.getSeller().subscribe((seller) => {
      latestSeller = seller;
    });

    service.refreshToken().subscribe();

    const req = httpMock.expectOne(`${environment.apiUrl}auth/refresh`);
    expect(req.request.method).toBe('POST');
    expect(req.request.withCredentials).toBeFalse();

    req.flush({
      success: true,
      data: { token: 'rotated-jwt', seller: updatedSeller },
    });

    expect(tokenStorage.getToken()).toBe('rotated-jwt');
    expect(latestSeller).toEqual(updatedSeller);
  });

  it('logOut() should POST auth/logout and clear token + reset seller$ even if HTTP fails with 500', () => {
    tokenStorage.setToken('active-jwt');
    service.nextSeller(mockSeller);

    let latestSeller = mockSeller as Seller | null;
    service.getSeller().subscribe((seller) => {
      latestSeller = seller;
    });

    let errorStatus: number | undefined;
    service.logOut().subscribe({
      error: (err) => {
        errorStatus = err.status;
      },
    });

    const req = httpMock.expectOne(`${environment.apiUrl}auth/logout`);
    expect(req.request.method).toBe('POST');
    expect(req.request.withCredentials).toBeFalse();

    req.flush(
      { message: 'Server error' },
      { status: 500, statusText: 'Internal Server Error' },
    );

    expect(errorStatus).toBe(500);
    expect(tokenStorage.getToken()).toBeNull();
    expect(tokenStorage.hasToken()).toBeFalse();
    expect(latestSeller).toBeNull();
  });

  it('resetPassword() should POST auth/change-password without withCredentials', () => {
    service.resetPassword('old-pass', 'new-pass').subscribe();

    const req = httpMock.expectOne(
      `${environment.apiUrl}auth/change-password`,
    );
    expect(req.request.method).toBe('POST');
    expect(req.request.withCredentials).toBeFalse();
    expect(req.request.body).toEqual({
      currentPassword: 'old-pass',
      newPassword: 'new-pass',
    });

    req.flush({ success: true, data: null });
  });
});
