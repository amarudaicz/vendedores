import { isDevMode } from '@angular/core';

export const environment = {
  apiUrl: isDevMode() ? 'http://localhost:8000/api/v1/' : '/api/v1/',
  empresa: {
    nombre: 'GREENDOR',
  }
};
   