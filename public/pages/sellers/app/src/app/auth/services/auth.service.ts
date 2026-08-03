import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { environment } from '../../../../environment';
import { Seller } from '../../interfaces/seller';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  constructor(private http: HttpClient) {}

  private seller: any;
  private seller$ = new BehaviorSubject<null | Seller>(null);

  logIn(values: any) {
    return this.http.post(`${environment.apiUrl}sellers/auth`, values, {
      withCredentials: true,
    });
  }

  logOut() {
    this.nextSeller(null);
    return this.http.get(`${environment.apiUrl}sellers/auth/sign-out`, {
      withCredentials: true,
    });
  }

  isLogged() {
    return this.http.get(`${environment.apiUrl}sellers/auth/is-logged`, {
      withCredentials: true,
    });
  }

  resetPassword({
    currentPassword,
    newPassword,
  }: {
    currentPassword: string;
    newPassword: string;
  }) {
    return this.http.post(
      `${environment.apiUrl}sellers/auth/change-password`,
      { currentPassword, newPassword },
      { withCredentials: true }
    );
  }

  getSeller() {
    return this.seller$;
  }

  nextSeller(seller: any) {
    this.seller = seller;
    this.seller$.next(seller);
  }

  isAdmin() {
    return this.seller?.isAdmin;
  }
}
