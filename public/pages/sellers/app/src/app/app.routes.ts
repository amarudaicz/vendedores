import { Routes } from '@angular/router';
import { LoginComponent } from './auth/components/login/login.component';
import { MainLayoutComponent } from './layout/main-layout/main-layout.component';
import { loginGuard } from './shared/guards/login/login.guard';
import { MainBalancesComponent } from './balances/components/main-balances/main-balances.component';
import { RouterComponent } from './router/router.component';
import { ResetPasswordComponent } from './auth/components/reset-password/reset-password.component';
import { authGuard } from './shared/guards/auth.guard';

export const routes: Routes = [
  {
    path: '',
    component: RouterComponent,
    children: [
      {
        path: 'login',
        component: LoginComponent,
      },
      {
        path: 'client/balance',
        component: MainBalancesComponent,
        canActivate: [authGuard],
        children: [
          {
            path: '',
            loadComponent: () =>
              import('./balances/components/customer-balance/customer-balance.component').then(
                (m) => m.CustomerBalanceComponent,
              ),
          },
          {
            path: 'orders',
            loadComponent: () =>
              import('./balances/components/customer-orders/customer-orders.component').then(
                (m) => m.CustomerOrdersComponent,
              ),
          },
          {
            path: 'orders/:id',
            loadComponent: () =>
              import('./orders/components/order-detail/order-detail.component').then(
                (m) => m.OrderDetailComponent,
              ),
          },
        ],
      },

      {
        path: '', //AKA ENTRA!!
        component: MainLayoutComponent,
        canActivate: [authGuard],
        children: [
          {
            path: '',
            loadComponent: () =>
              import('./dashboard/components/home/home.component').then(
                (m) => m.HomeComponent,
              ),
          },

          {
            path: 'clients',
            loadComponent: () =>
              import('./clients/components/main-clients/main-clients.component').then(
                (m) => m.MainClientsComponent,
              ),
          },
          {
            path: 'orders',
            loadComponent: () =>
              import('./orders/components/main-orders/main-orders.component').then(
                (m) => m.MainOrdersComponent,
              ),
          },
          {
            path: 'orders/:id',
            loadComponent: () =>
              import('./orders/components/order-detail/order-detail.component').then(
                (m) => m.OrderDetailComponent,
              ),
          },
          {
            path: 'orders/:id/modify',
            loadComponent: () =>
              import('./orders/components/order-modify/order-modify.component').then(
                (m) => m.OrderModifyComponent,
              ),
          },
          {
            path: 'login/reset-password',
            component: ResetPasswordComponent,
            canActivate: [loginGuard],
          },
          {
            path: 'manage-sellers',
            loadComponent: () =>
              import('./auth/components/manage-sellers/manage-sellers.component').then(
                (m) => m.ManageSellersComponent,
              ),
            canActivate: [authGuard],
          },
            {
            path: 'products',
            loadChildren: () =>
              import('./products/products.routes').then(m => m.PRODUCTS_ROUTES),
          },
          {
            path: 'cotizacion',
            loadComponent: () =>
              import('./cotizacion/components/main-cotizacion/main-cotizacion.component').then(
                (m) => m.MainCotizacionComponent,
              ),
          },
          {
            path: 'catalog',
            loadComponent: () =>
              import('./catalog/catalog.component').then(
                (m) => m.CatalogComponent,
              ),
          },
        ],
      },
      {
        path: '**',
        redirectTo: '',
      }
    ],
  },
];
