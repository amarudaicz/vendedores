import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { BehaviorSubject, map, Observable } from 'rxjs';
import { environment } from '@environments/environment';
import { OrderStats } from '../../../orders/interface/OrderInterface';
import { ApiRes } from '../../../interfaces/apiRes';
import { Order, OrderDetail, StatusKey } from '../../../interfaces/Order';
import { Product } from '../../../interfaces/product.interface';

interface OrderFilters {
  [key: string]: string | number | Date | boolean | null | undefined;
}

@Injectable({
  providedIn: 'root',
})
export class OrdersService {
  private statsSubject = new BehaviorSubject<OrderStats | undefined>(undefined);
  stats$ = this.statsSubject.asObservable();

  constructor(private http: HttpClient) {}

  getOrderById(id: number): Observable<ApiRes<OrderDetail>> {
    return this.http.get<ApiRes<OrderDetail>>(
      `${environment.apiUrl}orders/${id}`,
    );
  }

  postOrder(order: any): Observable<ApiRes<{ orderId: number }>> {
    return this.http.post<ApiRes<{ orderId: number }>>(
      `${environment.apiUrl}orders`,
      order,
    );
  }

  getOrders(
    filters: OrderFilters = {},
    page: number = 1,
    perPage: number = 10,
  ) {
    let params = new HttpParams()
      .set('page', page.toString())
      .set('perPage', perPage.toString());

    for (const key of Object.keys(filters)) {
      if (key === 'page' || key === 'perPage' || key === 'per_page') {
        continue;
      }
      const value = filters[key];
      if (value !== undefined && value !== null && value !== '') {
        params = params.append(key, String(value));
      }
    }

    return this.http
      .get<
        ApiRes<{
          orders: Order[];
          totalPages: number;
          total: number;
          stats: OrderStats;
        }>
      >(`${environment.apiUrl}orders`, { params })
      .pipe(
        map((res) => {
          this.statsSubject.next(res.data.stats);
          return res;
        }),
      );
  }

  getCsv(orderId: number): Observable<ApiRes<{ csv: string }>> {
    return this.http.get<ApiRes<{ csv: string }>>(
      `${environment.apiUrl}orders/${orderId}/csv`,
    );
  }

  updateOrderStatus(
    orderId: number,
    status: StatusKey | string,
  ): Observable<ApiRes<any>> {
    return this.http.put<ApiRes<any>>(
      `${environment.apiUrl}orders/${orderId}/status`,
      { status },
    );
  }

  updateOrderItems(orderId: number, items: any[]): Observable<ApiRes<any>> {
    return this.http.put<ApiRes<any>>(
      `${environment.apiUrl}orders/${orderId}/items`,
      { items },
    );
  }

  deleteOrder(orderId: number): Observable<ApiRes<any>> {
    return this.http.delete<ApiRes<any>>(
      `${environment.apiUrl}orders/${orderId}`,
    );
  }

  getStats(): Observable<ApiRes<{ stats: OrderStats }>> {
    return this.http.get<ApiRes<{ stats: OrderStats }>>(
      `${environment.apiUrl}stats`,
    );
  }

  searchProducts(
    query: string = '',
    customerPrice: number = 1,
    perPage: number = 500,
  ): Observable<ApiRes<{ products: Product[] }>> {
    const params = new HttpParams()
      .set('query', query)
      .set('customer_price', customerPrice.toString())
      .set('perPage', perPage.toString());

    return this.http.get<ApiRes<{ products: Product[] }>>(
      `${environment.apiUrl}products`,
      { params },
    );
  }
}