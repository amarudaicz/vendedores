import { provideHttpClient } from '@angular/common/http';
import {
  HttpTestingController,
  provideHttpClientTesting,
} from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { environment } from '@environments/environment';
import { CustomersService } from '../../clients/services/customers.service';
import { Customer } from '../../interfaces/customer';
import { Receipt } from '../../interfaces/payment';
import { BalanceService } from './balance.service';

describe('BalanceService', () => {
  let service: BalanceService;
  let customersService: CustomersService;
  let httpMock: HttpTestingController;

  const mockCustomer: Customer = {
    code: 'C100',
    dni: '20123456789',
    name: 'Cliente Demo',
    zone: 4,
    price_list: 1,
    descuento: 0,
    deleted: 0,
    seller_code: 10,
  };

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [provideHttpClient(), provideHttpClientTesting()],
    });

    service = TestBed.inject(BalanceService);
    customersService = TestBed.inject(CustomersService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    httpMock.verify();
  });

  it('getPayments() should GET payments?id=...&zone=... without withCredentials', () => {
    customersService.customer.set(mockCustomer);
    const mockReceipts: Receipt[] = [];

    let totalBalance: number | undefined;
    service.getPayments().subscribe((res) => {
      totalBalance = res.data.total_balance;
    });

    const req = httpMock.expectOne((r) => r.url === `${environment.apiUrl}payments`);
    expect(req.request.method).toBe('GET');
    expect(req.request.withCredentials).toBeFalse();
    expect(req.request.params.get('id')).toBe('C100');
    expect(req.request.params.get('zone')).toBe('4');

    req.flush({
      success: true,
      data: { receipts: mockReceipts, total_balance: 1500 },
    });

    expect(totalBalance).toBe(1500);
  });
});
