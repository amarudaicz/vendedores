import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { BehaviorSubject, map } from 'rxjs';
import { OrderStats } from '../../../orders/interface/OrderInterface';
import { environment } from '../../../../../environment';
import { ApiRes } from '../../../interfaces/apiRes';
import { Order } from '../../../interfaces/Order';

interface OrderFilters {
  [key: string]: string | number | Date | undefined;
}

@Injectable({
  providedIn: 'root'
})
export class OrdersService {

  private statsSubject = new BehaviorSubject<OrderStats|undefined>(undefined);
  stats$ = this.statsSubject.asObservable();

  constructor(private http: HttpClient) {

  }


  getOrder(id:number){
    return this.http.get(`${environment.apiUrl}orders/${id}`, { withCredentials: true })
  }

  postOrder(order: any) {
    return this.http.post(`${environment.apiUrl}sellers/orders`, order, { withCredentials: true })
  }

  getOrders(filters: OrderFilters = {}, page: number = 1, perPage: number = 10) {
    let params = new HttpParams()
      .set('page', page.toString())
      .set('perPage', perPage.toString());

    for (const key in filters) {
      if (filters[key] !== undefined && filters[key] !== null && filters[key] !== '') {
        params = params.append(key, String(filters[key]));
      }
    }

    return this.http.get<ApiRes<{orders:Order[], totalPages: number, total: number, stats:OrderStats}>>(
      `${environment.apiUrl}sellers/orders`,
      { withCredentials: true, params }
    ).pipe(
      map(res => {
        this.statsSubject.next(res.data.stats);
        return res;
      }
      )
    );
  }

  getOrderCsv(orderId:number){
    return this.http.get(`${environment.apiUrl}orders/${orderId}/csv`, { withCredentials: true })
  }

  updateOrder(status:any, orderId:number){
    return this.http.put(`${environment.apiUrl}orders/${orderId}/status`, {status}, { withCredentials: true })
  }

  updateOrderItems(orderId: number, data: any) {
    return this.http.put(`${environment.apiUrl}orders/${orderId}/items`, data, { withCredentials: true })
  }

  deleteOrder(orderId: number) {
    return this.http.delete(`${environment.apiUrl}orders/${orderId}`, { withCredentials: true })
  }

  getStats(){
    return this.http.get<{data:{stats:OrderStats}}>(`${environment.apiUrl}sellers/stats`, { withCredentials: true })
  }

  searchProducts(query: string = '', customerPrice: number = 1) {
    let params = new HttpParams()
      .set('query', query)
      .set('customer_price', customerPrice.toString())
      .set('perPage', '500'); // Traer más productos para el dropdown
    
    return this.http.get(`${environment.apiUrl}sellers/products`, { 
      withCredentials: true,
      params 
    });
  }



}