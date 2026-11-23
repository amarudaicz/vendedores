import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, finalize, tap } from 'rxjs';
import { environment } from '@environments/environment';
import { ApiRes } from '../../interfaces/apiRes';
import { Seller } from '../../interfaces/seller';
import { TokenStorageService } from './token-storage.service';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private readonly apiUrl = environment.apiUrl;
  private seller: Seller | null = null;
  private seller$ = new BehaviorSubject<Seller | null>(null);

  constructor(
    private http: HttpClient,
    private tokenStorage: TokenStorageService,
  ) {}

  logIn(credentials: {
    email: string;
    password: string;
  }): Observable<ApiRes<{ token: string; seller: Seller }>> {
    return this.http
      .post<ApiRes<{ token: string; seller: Seller }>>(
        `${this.apiUrl}auth/login`,
        credentials,
      )
      .pipe(
        tap((res) => {
          if (res?.data?.token) {
            this.tokenStorage.setToken(res.data.token);
          }
          if (res?.data?.seller) {
            this.nextSeller(res.data.seller);
          }
        }),
      );
  }

  logOut(): Observable<ApiRes<unknown>> {
    return this.http
      .post<ApiRes<unknown>>(`${this.apiUrl}auth/logout`, {})
      .pipe(
        finalize(() => {
          this.tokenStorage.clearToken();
          this.nextSeller(null);
        }),
      );
  }

  isLogged(): Observable<ApiRes<{ seller: Seller }>> {
    return this.http.get<ApiRes<{ seller: Seller }>>(`${this.apiUrl}auth/me`);
  }

  refreshToken(): Observable<ApiRes<{ token: string; seller?: Seller }>> {
    return this.http
      .post<ApiRes<{ token: string; seller?: Seller }>>(
        `${this.apiUrl}auth/refresh`,
        {},
      )
      .pipe(
        tap((res) => {
          if (res?.data?.token) {
            this.tokenStorage.setToken(res.data.token);
          }
          if (res?.data?.seller) {
            this.nextSeller(res.data.seller);
          }
        }),
      );
  }

  resetPassword(
    currentPassword: string,
    newPassword: string,
  ): Observable<ApiRes<unknown>>;
  resetPassword(payload: {
    currentPassword: string;
    newPassword: string;
  }): Observable<ApiRes<unknown>>;
  resetPassword(
    currentPasswordOrPayload:
      | string
      | { currentPassword: string; newPassword: string },
    newPassword?: string,
  ): Observable<ApiRes<unknown>> {
    const body =
      typeof currentPasswordOrPayload === 'string'
        ? {
            currentPassword: currentPasswordOrPayload,
            newPassword: newPassword ?? '',
          }
        : currentPasswordOrPayload;

    return this.http.post<ApiRes<unknown>>(
      `${this.apiUrl}auth/change-password`,
      body,
    );
  }

  getSeller(): Observable<Seller | null> {
    return this.seller$.asObservable();
  }

  nextSeller(seller: Seller | null): void {
    this.seller = seller;
    this.seller$.next(seller);
  }

  isAdmin(): boolean {
    return Boolean(this.seller?.isAdmin);
  }
}
