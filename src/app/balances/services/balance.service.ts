import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { environment } from '@environments/environment';
import { CustomersService } from '../../clients/services/customers.service';
import { ApiRes } from '../../interfaces/apiRes';
import { Receipt } from '../../interfaces/payment';

@Injectable({
  providedIn: 'root',
})
export class BalanceService {
  receipts = signal<any[] | undefined>(undefined);
  totalBalance = signal<number>(0);
  lastReceiptDate = signal<string>('');

  constructor(
    private http: HttpClient,
    private customerService: CustomersService,
  ) {}

  getPayments() {
    const customer_code = this.customerService.customer()?.code || '';
    const customer_zone = this.customerService.customer()?.zone || '';
    const params = new HttpParams()
      .set('id', String(customer_code))
      .set('zone', String(customer_zone));

    return this.http.get<ApiRes<{ receipts: Receipt[]; total_balance: number }>>(
      `${environment.apiUrl}payments`,
      { params },
    );
  }
}
