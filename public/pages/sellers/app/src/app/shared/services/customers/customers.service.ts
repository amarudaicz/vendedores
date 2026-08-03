import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Customer } from '../../../interfaces/customer';
import { environment } from '../../../../../environment';

@Injectable({
  providedIn: 'root'
})
export class CustomersService {

  constructor(private http:HttpClient) {

  }

   getCustomers(query:string = ''){
      return this.http.get<{data:{customers:Customer[]}}>(`${environment.apiUrl}sellers/customers?query=${query}`, {withCredentials:true})
   }
}
