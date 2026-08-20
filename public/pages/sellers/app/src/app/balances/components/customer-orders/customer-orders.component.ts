import { CommonModule, CurrencyPipe } from '@angular/common';
import { Component, ElementRef, ViewChild } from '@angular/core';
import { NgxSkeletonLoaderModule } from 'ngx-skeleton-loader';
import { BalanceService } from '../../services/balance.service';
import { CustomersService } from '../../../clients/services/customers.service';
import { Order, STATUS_LABELS, StatusKey } from '../../../interfaces/Order';
import { Router } from '@angular/router';
import { OrdersService } from '../../../shared/services/orders/orders.service';

@Component({
  selector: 'app-customer-orders',
  imports: [CommonModule, NgxSkeletonLoaderModule, CurrencyPipe],
  templateUrl: './customer-orders.component.html',
  styleUrl: './customer-orders.component.scss',
})
export class CustomerOrdersComponent {
  traduceStatus = STATUS_LABELS;
  orders?: Order[];

  @ViewChild('scrollAnchor', { static: true }) scrollAnchor!: ElementRef;
  @ViewChild('scrollContainer', { static: true }) scrollContainer!: ElementRef;
  paginator: any = {
    page: 1,
    totalPages: 0,
    totalOrders: 0,
  };

  constructor(
    private router: Router,
    private ordersService: OrdersService,
    private BalanceService: BalanceService,
    private customerService: CustomersService,
  ) {
    const customer_code = this.customerService.customer()?.code;
    this.ordersService
      .getOrders({ customer_code: customer_code as string })
      .subscribe((res: any) => {
        this.orders = res.data.orders;
        this.paginator.totalPages = res.data.totalPages;
        this.paginator.totalOrders = res.data.total;
        setTimeout(() => this.setupObservers(), 500);
      });
  }

  viewOrder(order: Order) {
    this.router.navigate(['client/balance/orders', order.id]);
  }

  getTranslatedStatus(status: StatusKey): string {
    return this.traduceStatus[status]; // Ahora TypeScript sabe que `status` es uno de los valores definidos en `StatusKey`
  }

  getTimeAgo(created_at: string): string {
    const date = new Date(created_at);
    const now = new Date();
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

    const intervals: { [key: string]: number } = {
      año: 31536000,
      mes: 2592000,
      semana: 604800,
      día: 86400,
      hora: 3600,
      minuto: 60,
      segundo: 1,
    };

    for (const key in intervals) {
      const interval = Math.floor(diffInSeconds / intervals[key]);
      if (interval >= 1) {
        return interval === 1 ? `Hace 1 ${key}` : `Hace ${interval} ${key}`;
      }
    }

    return 'Hace un momento';
  }

  loadMoreOrders() {
    if (this.paginator.page === this.paginator.totalPages) return;

    const customer_code = this.customerService.customer()?.code;

    this.ordersService
      .getOrders({
        customer_code: customer_code as string,
        page: this.paginator.page + 1,
      })
      .pipe()
      .subscribe((res: any) => {
        this.orders = [...(this.orders || []), ...res.data.orders];
        this.paginator.page++;
      });
  }

  setupObservers() {
    const container = this.scrollContainer.nativeElement;
    const anchor = this.scrollAnchor.nativeElement;
    if (anchor && container) {
      this.setupIntersectionObserver(anchor, container);
    }
  }

  setupIntersectionObserver(anchor: HTMLElement, container: HTMLElement) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            this.loadMoreOrders();
          }
        });
      },
      {
        root: container, // El contenedor con overflow-scroll
        rootMargin: '0px',
        threshold: 0.1, // Se activa cuando el 10% del elemento está visible
      },
    );
    observer.observe(anchor);
  }
}
