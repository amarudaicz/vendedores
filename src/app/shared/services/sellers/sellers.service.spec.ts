import { provideHttpClient } from '@angular/common/http';
import {
  HttpTestingController,
  provideHttpClientTesting,
} from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { environment } from '@environments/environment';
import { Seller } from '../../../interfaces/seller';
import { SellersService } from './sellers.service';

describe('SellersService', () => {
  let service: SellersService;
  let httpMock: HttpTestingController;

  const mockSellers: Seller[] = [
    {
      id: 1,
      name: 'Carlos',
      email: 'carlos@example.com',
      code: 10,
      deleted: false,
      createdAt: '2026-01-01T00:00:00Z',
    },
  ];

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [provideHttpClient(), provideHttpClientTesting()],
    });

    service = TestBed.inject(SellersService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    httpMock.verify();
  });

  it('should NOT fire any eager HTTP request on construction', () => {
    httpMock.expectNone(() => true);
    expect(service.sellers()).toBeNull();
  });

  it('getSellers() should GET sellers without withCredentials and update sellers signal', () => {
    let result: Seller[] | undefined;

    service.getSellers().subscribe((sellers) => {
      result = sellers;
    });

    const req = httpMock.expectOne(`${environment.apiUrl}sellers`);
    expect(req.request.method).toBe('GET');
    expect(req.request.withCredentials).toBeFalse();
    req.flush({ success: true, data: mockSellers });

    expect(result).toEqual(mockSellers);
    expect(service.sellers()).toEqual(mockSellers);
  });

  it('updatePassword() should POST auth/change-password without withCredentials', () => {
    service.updatePassword('new-secret', 'old-secret', 1).subscribe();

    const req = httpMock.expectOne(
      `${environment.apiUrl}auth/change-password`,
    );
    expect(req.request.method).toBe('POST');
    expect(req.request.withCredentials).toBeFalse();
    expect(req.request.body).toEqual({
      newPassword: 'new-secret',
      currentPassword: 'old-secret',
      sellerId: 1,
    });
    req.flush({ success: true, data: null });
  });
});
