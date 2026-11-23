import { CanActivateFn, Router } from '@angular/router';
import { inject } from '@angular/core';
import { TokenStorageService } from '../../../auth/services/token-storage.service';

export const loginGuard: CanActivateFn = () => {
  const tokenStorage = inject(TokenStorageService);
  const router = inject(Router);

  if (tokenStorage.hasToken()) {
    return router.createUrlTree(['/']);
  }

  return true;
};
