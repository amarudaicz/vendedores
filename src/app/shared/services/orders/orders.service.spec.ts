import { provideHttpClient } from '@angular/common/http';
import {
  HttpTestingController,
  provideHttpClientTesting,
} from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { environment } from '@environments/environment';
import { OrderStats } from '../../../orders/interface/OrderInterface';
import { Order, OrderDetail } from '../../../interfaces/Order';
import { OrdersService } from './orders.service';

describe('OrdersService', () => {
  let service: OrdersService;
  let httpMock: HttpTestingController;

  const mockStats: OrderStats = {
    total_orders: { count: 10, percentage: 100 },
    pending_orders: { count: 3, percentage: 30 },
    finalized_orders: { count: 6, percentage: 60 },
    not_realized_orders: { count: 1, percentage: 10 },
    total_invoiced: { count: 6, percentage: 60 },
  };

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [provideHttpClient(), provideHttpClientTesting()],
    });

    service = TestBed.inject(OrdersService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    httpMock.verify();
  });

  it('should use /api/seller/ base apiUrl', () => {
    expect(environment.apiUrl.endsWith('/api/seller/')).toBeTrue();
  });

  it('getOrders() should send a SINGLE page param (ignoring duplicate filters.page), update stats$, and keep withCredentials false', () => {
    let latestStats: OrderStats | undefined;
    service.stats$.subscribe((stats) => {
      latestStats = stats;
    });

    service
      .getOrders(
        {
          page: 99,
          perPage: 999,
          per_page: 500,
          status: 'pending',
          search: '',
          emptyNull: null,
        },
        2,
        15,
      )
      .subscribe();

    const req = httpMock.expectOne((r) => r.url === `${environment.apiUrl}orders`);
    expect(req.request.method).toBe('GET');
    expect(req.request.withCredentials).toBeFalse();
    expect(req.request.params.getAll('page')).toEqual(['2']);
    expect(req.request.params.getAll('perPage')).toEqual(['15']);
    expect(req.request.params.has('per_page')).toBeFalse();
    expect(req.request.params.get('status')).toBe('pending');
    expect(req.request.params.has('search')).toBeFalse();
    expect(req.request.params.has('emptyNull')).toBeFalse();

    req.flush({
      success: true,
      data: {
        orders: [] as Order[],
        totalPages: 1,
        total: 0,
        stats: mockStats,
      },
    });

    expect(latestStats).toEqual(mockStats);
  });

  it('getOrderById() should GET /api/seller/orders/:id without withCredentials', () => {
    const mockOrder = { id: 42, status: 'pending', items: [] } as unknown as OrderDetail;
    let result: OrderDetail | undefined;

    service.getOrderById(42).subscribe((res) => {
      result = res.data;
    });

    const req = httpMock.expectOne(`${environment.apiUrl}orders/42`);
    expect(req.request.method).toBe('GET');
    expect(req.request.withCredentials).toBeFalse();
    req.flush({ success: true, data: mockOrder });

    expect(result).toEqual(mockOrder);
  });

  it('postOrder() should POST /api/seller/orders without withCredentials', () => {
    const payload = { customerCode: 'C1', items: [] };
    service.postOrder(payload).subscribe();

    const req = httpMock.expectOne(`${environment.apiUrl}orders`);
    expect(req.request.method).toBe('POST');
    expect(req.request.withCredentials).toBeFalse();
    expect(req.request.body).toEqual(payload);
    req.flush({ success: true, data: { orderId: 100 } });
  });

  it('getCsv() should GET /api/seller/orders/:id/csv without withCredentials', () => {
    service.getCsv(42).subscribe();

    const req = httpMock.expectOne(`${environment.apiUrl}orders/42/csv`);
    expect(req.request.method).toBe('GET');
    expect(req.request.withCredentials).toBeFalse();
    req.flush({ success: true, data: { csv: 'a,b\n1,2' } });
  });

  it('updateOrderStatus() should PUT /api/seller/orders/:id/status without withCredentials', () => {
    service.updateOrderStatus(42, 'confirmed').subscribe();

    const req = httpMock.expectOne(`${environment.apiUrl}orders/42/status`);
    expect(req.request.method).toBe('PUT');
    expect(req.request.withCredentials).toBeFalse();
    expect(req.request.body).toEqual({ status: 'confirmed' });
    req.flush({ success: true, data: null });
  });

  it('updateOrderItems() should PUT /api/seller/orders/:id/items without withCredentials', () => {
    const items = [{ productCode: 'P1', quantity: 2, price: 10 }];
    service.updateOrderItems(42, items).subscribe();

    const req = httpMock.expectOne(`${environment.apiUrl}orders/42/items`);
    expect(req.request.method).toBe('PUT');
    expect(req.request.withCredentials).toBeFalse();
    expect(req.request.body).toEqual({ items });
    req.flush({ success: true, data: null });
  });

  it('deleteOrder() should DELETE /api/seller/orders/:id without withCredentials', () => {
    service.deleteOrder(42).subscribe();

    const req = httpMock.expectOne(`${environment.apiUrl}orders/42`);
    expect(req.request.method).toBe('DELETE');
    expect(req.request.withCredentials).toBeFalse();
    req.flush({ success: true, data: null });
  });

  it('getStats() should GET /api/seller/stats without withCredentials', () => {
    service.getStats().subscribe();

    const req = httpMock.expectOne(`${environment.apiUrl}stats`);
    expect(req.request.method).toBe('GET');
    expect(req.request.withCredentials).toBeFalse();
    req.flush({ success: true, data: { stats: mockStats } });
  });

  it('searchProducts() should GET /api/seller/products without withCredentials', () => {
    service.searchProducts('cable', 2, 100).subscribe();

    const req = httpMock.expectOne((r) => r.url === `${environment.apiUrl}products`);
    expect(req.request.method).toBe('GET');
    expect(req.request.withCredentials).toBeFalse();
    expect(req.request.params.get('query')).toBe('cable');
    expect(req.request.params.get('customer_price')).toBe('2');
    expect(req.request.params.get('perPage')).toBe('100');
    req.flush({ success: true, data: { products: [] } });
  });
});
