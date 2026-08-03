import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../../auth/services/auth.service';
import { inject } from '@angular/core';
import { of } from 'rxjs';
import { catchError, map, switchMap, take } from 'rxjs/operators';

export const authGuard: CanActivateFn = (route, state) => {
  const auth = inject(AuthService);
  const router = inject(Router);

  return auth.getSeller().pipe(
    take(1),
    switchMap((seller) => {
      if (seller) {
        return of(true);
      }

      return auth.isLogged().pipe(
        map((res: any) => {
          auth.nextSeller(res.data.seller);
          if (res.data.seller) {
            return true;
          }
          router.navigate(['/login'], {
            queryParams: { returnUrl: state.url },
          });
          return false;
        }),
        catchError(() => {
          router.navigate(['/login'], {
            queryParams: { returnUrl: state.url },
          });
          return of(false);
        })
      );
    })
  );
};
