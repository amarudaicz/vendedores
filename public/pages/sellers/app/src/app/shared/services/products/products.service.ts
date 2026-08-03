import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../../../../environment';
import { BehaviorSubject, map } from 'rxjs';
import { ApiRes } from '../../../interfaces/apiRes';

@Injectable({
  providedIn: 'root',
})
export class ProductsService {
  constructor(private http: HttpClient) {
    this.getDolar();
  }

  public dolar$ = new BehaviorSubject<number>(0);

  getDolar() {
    return this.http
      .get<ApiRes<{ dolar: number }>>(`${environment.apiUrl}sellers/dolar`, {
        withCredentials: true,
      })
      .pipe(map((res) => res.data.dolar))
      .subscribe((res) => this.dolar$.next(res));
  }
}
