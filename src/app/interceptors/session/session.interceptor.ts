import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError } from 'rxjs/operators';
import { throwError } from 'rxjs';
import AlertService from '../../shared/services/alert/alert.service';
import { TokenStorageService } from '../../auth/services/token-storage.service';
import { AuthService } from '../../auth/services/auth.service';

export const SessionInterceptor: HttpInterceptorFn = (req, next) => {
  const router = inject(Router);
  const alerts = inject(AlertService);
  const tokenStorage = inject(TokenStorageService);
  const authService = inject(AuthService);

  return next(req).pipe(
    catchError((error: unknown) => {
      if (
        !req.url.includes('/auth/login') &&
        error instanceof HttpErrorResponse &&
        error.status === 401
      ) {
        tokenStorage.clearToken();
        authService.nextSeller(null);

        if (!router.url.startsWith('/login')) {
          alerts.showAlert(
            'Sesión expirada, por favor inicie sesión nuevamente',
            'info',
          );
          router.navigate(['/login']);
        }
      }

      return throwError(() => error);
    }),
  );
};
