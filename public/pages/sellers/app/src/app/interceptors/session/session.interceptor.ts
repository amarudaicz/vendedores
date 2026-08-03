import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError } from 'rxjs/operators';
import { throwError } from 'rxjs';
import AlertService from '../../shared/services/alert/alert.service';

export const SessionInterceptor: HttpInterceptorFn = (req, next) => {
  const router = inject(Router);
  const alerts = inject(AlertService);

  return next(req).pipe(
    catchError((error) => {
      if (error.status === 401) {
        alerts.showAlert('Sesión expirada, por favor inicie sesión nuevamente', 'info');
        // Redirigir al login si el error es 401 Unauthorized
        if (router.url !== '/login') {
          router.navigate(['/login']);
        }
      }
      return throwError(() => error);
    }),

  );
};
