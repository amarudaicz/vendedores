import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '@environments/environment';
import { Customer } from '../../../interfaces/customer';

@Injectable({
  providedIn: 'root',
})
export class CustomersService {
  constructor(private http: HttpClient) {}

  getCustomers(query: string = '') {
    const params = new HttpParams().set('query', query);
    return this.http.get<{ data: { customers: Customer[] } }>(
      `${environment.apiUrl}customers`,
      { params },
    );
  }
}
