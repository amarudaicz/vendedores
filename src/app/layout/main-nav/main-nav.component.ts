import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import {
  NavigationEnd,
  Router,
  RouterLink,
  RouterModule,
} from '@angular/router';
import { filter, Subject, takeUntil } from 'rxjs';
import { AuthService } from '../../auth/services/auth.service';
import { globalConfig } from '../../global-config';
import { Seller } from '../../interfaces/seller';

const MANAGE_SELLERS_NAV_ITEM = {
  id: 5,
  link: 'manage-sellers',
  label: 'Vendedores',
  icon: 'icon-[icon-park-outline--every-user]',
};

@Component({
  selector: 'app-main-nav',
  imports: [RouterLink, CommonModule, RouterModule],
  templateUrl: './main-nav.component.html',
  styleUrl: './main-nav.component.scss',
})
export class MainNavComponent implements OnInit, OnDestroy {
  navItems: any[] = [
    {
      id: 1,
      link: '',
      label: 'Nueva Nota de Pedido',
      icon: 'icon-[icon-park-outline--add]',
    },
    {
      id: 2,
      link: 'clients',
      label: 'Clientes',
      icon: 'icon-[icon-park-outline--user-positioning]',
    },
    {
      id: 3,
      link: 'products',
      label: 'Productos',
      icon: 'icon-[icon-park-outline--box]',
    },
    {
      id: 4,
      link: 'orders',
      label: 'Mis Notas de Pedido',
      icon: 'icon-[icon-park-outline--transaction-order]',
    },
    globalConfig.cotizacion
      ? {
          id: 6,
          link: 'cotizacion',
          label: 'Cotización',
          icon: 'icon-[icon-park-outline--dollar]',
        }
      : null,
  ].filter((i) => i !== null);

  pathActive: string = '';
  seller: Seller | null = null;
  private destroy$ = new Subject<void>();

  constructor(
    private router: Router,
    private auth: AuthService,
  ) {
    this.router.events
      .pipe(
        filter((event): event is NavigationEnd => event instanceof NavigationEnd),
        takeUntil(this.destroy$),
      )
      .subscribe((event) => {
        this.pathActive = event.urlAfterRedirects.replace(/^\//, '');
      });
  }

  ngOnInit() {
    this.auth
      .getSeller()
      .pipe(takeUntil(this.destroy$))
      .subscribe((seller) => {
        this.seller = seller;
        const hasManageSellers = this.navItems.some(
          (item) => item.link === 'manage-sellers',
        );

        if (seller?.isAdmin) {
          if (!hasManageSellers) {
            this.navItems = [...this.navItems, MANAGE_SELLERS_NAV_ITEM];
          }
        } else if (hasManageSellers) {
          this.navItems = this.navItems.filter(
            (item) => item.link !== 'manage-sellers',
          );
        }
      });
  }

  ngOnDestroy() {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
