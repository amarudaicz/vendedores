import {
  ApplicationConfig,
  provideZoneChangeDetection,
  LOCALE_ID,
  DEFAULT_CURRENCY_CODE,
} from '@angular/core';
import { provideRouter, withComponentInputBinding } from '@angular/router';

import { routes } from './app.routes';
import {
  HTTP_INTERCEPTORS,
  HttpClient,
  provideHttpClient,
  withInterceptors,
} from '@angular/common/http';
import { provideAnimationsAsync } from '@angular/platform-browser/animations/async';
import { SessionInterceptor } from './interceptors/session/session.interceptor';
import { registerLocaleData } from '@angular/common';
import localeEsAr from '@angular/common/locales/es-AR';
import { provideStore } from '@ngrx/store';
import { AuthService } from './auth/services/auth.service';
import { catchError, firstValueFrom, of, tap } from 'rxjs';

const localeData = [...localeEsAr];
const currencyMap = localeData[18] as Record<string, (string | undefined)[]>;
if (currencyMap['USD']) {
  currencyMap['USD'] = ['$', '$'];
}
registerLocaleData(localeData, 'es-AR');

export const appConfig: ApplicationConfig = {
  providers: [
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes, withComponentInputBinding()),
    provideHttpClient(withInterceptors([SessionInterceptor])),
    provideAnimationsAsync(),
    provideStore({}),
    { provide: LOCALE_ID, useValue: 'en-US' },
    { provide: DEFAULT_CURRENCY_CODE, useValue: 'USD' },
  ],
};
