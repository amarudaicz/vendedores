import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, signal, Signal } from '@angular/core';
import { environment } from '../../../../../environment';
import { map, Observable } from 'rxjs';
import { Seller } from '../../../interfaces/seller';
import { ApiRes } from '../../../interfaces/apiRes';

@Injectable({
  providedIn: 'root',
})
export class SellersService {
  public sellers = signal<Seller[]|null>(null);
  private seller = signal<Seller|null>(null);
  constructor(private http: HttpClient) {
    if(!this.sellers()){
      this.getSellers().subscribe((res) => {
        this.sellers.set(res);
      });
    }
  }

  getSellers() {
    return this.http
      .get<ApiRes<Seller[]>>(`${environment.apiUrl}sellers/all`, { withCredentials: true })
      .pipe(map((res) => {
        this.sellers.set(res.data)
        return res.data
      }));
  }

  getSeller(code: number) {
    return this.seller();
  }

  updatePassword(
    newPassword: string,
    currentPassword?: string,
    sellerId?: number,
  ) {
    const payload: any = { newPassword };
    if (currentPassword) payload.currentPassword = currentPassword;
    if (sellerId) payload.sellerId = sellerId;

    return this.http.post<any>(
      `${environment.apiUrl}sellers/auth/change-password`,
      payload,
      { withCredentials: true },
    );
  }
}
