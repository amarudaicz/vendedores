import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { Customer } from '../../interfaces/customer';
import { ApiRes } from '../../interfaces/apiRes';
import { environment } from '../../../../environment';

@Injectable({
  providedIn: 'root'
})
export class CustomersService {

  constructor(private http: HttpClient) {

  }

  public customer = signal<Customer | null>(null);


  getCustomers(query: string = '', sellerCode: string | number = '') {
    return this.http.get<ApiRes<{ customers: Customer[]}>>(`${environment.apiUrl}sellers/customers?query=${query}&sellerCode=${sellerCode}`, { withCredentials: true })
  }
}
