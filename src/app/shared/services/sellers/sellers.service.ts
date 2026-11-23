import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { map } from 'rxjs';
import { environment } from '@environments/environment';
import { Seller } from '../../../interfaces/seller';
import { ApiRes } from '../../../interfaces/apiRes';

@Injectable({
  providedIn: 'root',
})
export class SellersService {
  public sellers = signal<Seller[] | null>(null);
  private seller = signal<Seller | null>(null);

  constructor(private http: HttpClient) {}

  getSellers() {
    return this.http
      .get<ApiRes<Seller[]>>(`${environment.apiUrl}sellers`)
      .pipe(
        map((res) => {
          this.sellers.set(res.data);
          return res.data;
        }),
      );
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
      `${environment.apiUrl}auth/change-password`,
      payload,
    );
  }
}
