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

  searchProducts(query: string, customerPrice: number = 1): Observable<ApiRes<{ products: Product[] }>> {
    const params = new URLSearchParams({
      query,
      customer_price: customerPrice.toString(),
    });
    return this.http.get<ApiRes<{ products: Product[] }>>(
      `${environment.apiUrl}sellers/products?${params.toString()}`,
      { withCredentials: true }
    );
  }
}