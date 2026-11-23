import {
  HttpClient,
  provideHttpClient,
  withInterceptors,
} from '@angular/common/http';
import {
  HttpTestingController,
  provideHttpClientTesting,
} from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { TokenStorageService } from '../../auth/services/token-storage.service';
import { authTokenInterceptor } from './auth-token.interceptor';

describe('authTokenInterceptor', () => {
  let http: HttpClient;
  let httpMock: HttpTestingController;
  let tokenStorageSpy: jasmine.SpyObj<TokenStorageService>;

  beforeEach(() => {
    tokenStorageSpy = jasmine.createSpyObj<TokenStorageService>(
      'TokenStorageService',
      ['getToken'],
    );

    TestBed.configureTestingModule({
      providers: [
        provideHttpClient(withInterceptors([authTokenInterceptor])),
        provideHttpClientTesting(),
        { provide: TokenStorageService, useValue: tokenStorageSpy },
      ],
    });

    http = TestBed.inject(HttpClient);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    httpMock.verify();
  });

  it('should attach Authorization Bearer header and never set withCredentials when token exists', () => {
    tokenStorageSpy.getToken.and.returnValue('secret-jwt');

    http.get('/api/seller/orders').subscribe();

    const req = httpMock.expectOne('/api/seller/orders');
    expect(req.request.headers.get('Authorization')).toBe('Bearer secret-jwt');
    expect(req.request.withCredentials).toBeFalse();
    req.flush({});
  });

  it('should omit Authorization header and never set withCredentials when no token is present', () => {
    tokenStorageSpy.getToken.and.returnValue(null);

    http.get('/api/seller/orders').subscribe();

    const req = httpMock.expectOne('/api/seller/orders');
    expect(req.request.headers.has('Authorization')).toBeFalse();
    expect(req.request.withCredentials).toBeFalse();
    req.flush({});
  });
});
