import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../../environment';
import { ApiRes } from '../../interfaces/apiRes';
import { Product } from '../../interfaces/product.interface';

@Injectable({
  providedIn: 'root'
})
export class ProductService {

  constructor(private http: HttpClient) { }

  getProducts(page: number = 1): Observable<ApiRes<{ products: Product[], totalPages: number, total: number }>> {
    return this.http.get<ApiRes<{ products: Product[], totalPages: number, total: number }>>(`${environment.apiUrl}products?page=${page}`, { withCredentials: true });
  }
}