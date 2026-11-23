import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map, Observable } from 'rxjs';
import { environment } from '@environments/environment';
import { ApiRes } from '../../interfaces/apiRes';

@Injectable({
  providedIn: 'root',
})
export class CotizacionService {
  constructor(private http: HttpClient) {}

  getDolar(): Observable<number> {
    return this.http
      .get<ApiRes<{ dolar: number }>>(`${environment.apiUrl}dolar`)
      .pipe(map((res) => res.data.dolar));
  }

  updateDolar(rate: number): Observable<ApiRes<any>> {
    return this.http.put<ApiRes<any>>(`${environment.apiUrl}dolar`, {
      dolar: rate,
    });
  }
}
