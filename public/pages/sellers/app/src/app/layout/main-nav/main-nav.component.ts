import { AsyncPipe, CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import {
  ActivatedRoute,
  ActivationEnd,
  NavigationEnd,
  Router,
  RouterEvent,
  RouterLink,
  RouterModule,
} from '@angular/router';
import { filter, Observable } from 'rxjs';
import { AuthService } from '../../auth/services/auth.service';
import { globalConfig } from '../../global-config';

@Component({
  selector: 'app-main-nav',
  imports: [RouterLink, CommonModule, RouterModule],
  templateUrl: './main-nav.component.html',
  styleUrl: './main-nav.component.scss',
})
export class MainNavComponent implements OnInit {
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
  seller: any = null;

  constructor(
    private router: Router,
    private auth: AuthService,
  ) {
    this.router.events
      .pipe(filter((event) => event instanceof NavigationEnd))
      .subscribe((event: NavigationEnd) => {
        this.pathActive = event.urlAfterRedirects.replace(/^\//, '');
      });
  }

  ngOnInit() {
    this.auth.getSeller().subscribe((seller: any) => {
      this.seller = seller;
      if (seller.isAdmin) {
        this.navItems.push({
          id: 5,
          link: 'manage-sellers',
          label: 'Vendedores',
          icon: 'icon-[icon-park-outline--every-user]',
        });
      }
    });
  }
}
