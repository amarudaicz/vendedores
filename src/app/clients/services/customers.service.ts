import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { environment } from '@environments/environment';
import { Customer } from '../../interfaces/customer';
import { ApiRes } from '../../interfaces/apiRes';

@Injectable({
  providedIn: 'root',
})
export class CustomersService {
  constructor(private http: HttpClient) {}

  public customer = signal<Customer | null>(null);

  getCustomers(query: string = '', sellerCode: string | number = '') {
    let params = new HttpParams().set('query', query);
    if (sellerCode !== '' && sellerCode !== null && sellerCode !== undefined) {
      params = params.set('sellerCode', String(sellerCode));
    }

    return this.http.get<ApiRes<{ customers: Customer[] }>>(
      `${environment.apiUrl}customers`,
      { params },
    );
  }
}
