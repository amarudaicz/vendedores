import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../../auth/services/auth.service';
import { TokenStorageService } from '../../auth/services/token-storage.service';
import { inject } from '@angular/core';
import { of } from 'rxjs';
import { catchError, map, switchMap, take } from 'rxjs/operators';

export const authGuard: CanActivateFn = (_route, state) => {
  const auth = inject(AuthService);
  const tokenStorage = inject(TokenStorageService);
  const router = inject(Router);

  if (!tokenStorage.hasToken()) {
    return router.createUrlTree(['/login'], {
      queryParams: { returnUrl: state.url },
    });
  }

  return auth.getSeller().pipe(
    take(1),
    switchMap((seller) => {
      if (seller) {
        return of(true);
      }

      return auth.isLogged().pipe(
        map((res) => {
          if (res?.data?.seller) {
            auth.nextSeller(res.data.seller);
            return true;
          }

          tokenStorage.clearToken();
          auth.nextSeller(null);
          return router.createUrlTree(['/login'], {
            queryParams: { returnUrl: state.url },
          });
        }),
        catchError(() => {
          tokenStorage.clearToken();
          auth.nextSeller(null);
          return of(
            router.createUrlTree(['/login'], {
              queryParams: { returnUrl: state.url },
            }),
          );
        }),
      );
    }),
  );
};
