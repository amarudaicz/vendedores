import { CommonModule } from '@angular/common';
import {
  AfterContentInit,
  Component,
  ElementRef,
  HostListener,
  OnInit,
  QueryList,
  ViewChild,
  ViewChildren,
} from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule } from '@angular/forms';
import {
  catchError,
  debounceTime,
  distinctUntilChanged,
  of,
  Subject,
} from 'rxjs';
import { Router } from '@angular/router';
import { NgxSkeletonLoaderModule } from 'ngx-skeleton-loader';
import AlertService from '../../../shared/services/alert/alert.service';
import { OrdersService } from '../../../shared/services/orders/orders.service';
import { SellersService } from '../../../shared/services/sellers/sellers.service';
import { AuthService } from '../../../auth/services/auth.service';
import { SearchClientComponent } from '../../../shared/components/search-client/search-client.component';
import { Seller } from '../../../interfaces/seller';
import { Order } from '../../../interfaces/Order';
type StatusKey = 'pending' | 'finalized' | 'confirmed' | 'in_progress';

@Component({
  selector: 'app-orders-list',
  imports: [
    CommonModule,
    NgxSkeletonLoaderModule,
    ReactiveFormsModule,
    SearchClientComponent,
  ],
  templateUrl: './orders-list.component.html',
  styleUrl: './orders-list.component.scss',
})
export class OrdersListComponent implements OnInit, AfterContentInit {
  constructor(
    private ordersService: OrdersService,
    private router: Router,
    private alert: AlertService,
    private fb: FormBuilder,
    public authService: AuthService,
    private sellersService: SellersService,
  ) {
    this.filterForm = this.fb.group({
      search: [''], // Usado internamente por SearchClientComponent
      orderId: [''],
      status: [''],
      dateFrom: [''],
      dateTo: [''],
      sellerCode: [''],
      customerCode: [''],
    });
  }

  loading = false;
  loadingPage = false;
  orders: Order[] | undefined = undefined;
  filterOrders: any[] | undefined = [];
  @ViewChild('scrollContainer', { static: true }) scrollContainer!: ElementRef;
  @ViewChildren('menuOptions') menuOptions!: QueryList<ElementRef>;
  filterForm: FormGroup;
  showFilters = false;
  private searchSubject = new Subject<string>();
  sellers: Seller[] = [];

  traduceStatus: { [key in StatusKey]: string } = {
    pending: 'Pendiente',
    finalized: 'Enviado',
    confirmed: 'Confirmado',
    in_progress: 'En proceso',
  };

  paginator: any = {
    page: 1,
    totalPages: 0,
    totalOrders: 0,
  };

  ngOnInit() {
    this.fetchOrders();

    if (this.authService.isAdmin()) {
      this.sellersService.getSellers().subscribe((res) => {
        this.sellers = res;
      });
    }

    // // Configurar debounce para búsqueda
    // this.searchSubject.pipe(
    //   debounceTime(500),
    //   distinctUntilChanged()
    // ).subscribe(() => {
    //   this.applyFilters();
    // });
  }

  fetchOrders(filters = {}, page = 1) {
    this.loading = true;

    this.ordersService.getOrders(filters, page).subscribe((res: any) => {
      this.orders = res.data.orders;
      this.filterOrders = this.orders;
      this.paginator.page = page;
      this.paginator.totalPages = res.data.totalPages;
      this.paginator.totalOrders = res.data.total;
      this.loading = false;
      this.loadingPage = false;
    });
  }

  ngAfterContentInit(): void {}

  @HostListener('document:click', ['$event'])
  onClickOutside(event: Event) {
    const targetElement = event.target as HTMLElement;
    let clickedInside = false;

    this.menuOptions?.forEach((menu) => {
      if (menu.nativeElement.contains(targetElement)) {
        clickedInside = true;
      }
    });

    if (!clickedInside) {
      this.filterOrders?.forEach((order) => (order.showOptions = false));
    }
  }

  showOptionsStatus(order: any, event: any) {
    this.filterOrders?.forEach((order) => (order.showOptions = false));
    order.showOptions = !order.showOptions;
  }

  getOptionsStatus(order: { status: StatusKey }): StatusKey[] {
    // Si la orden está finalizada, no mostrar opciones de cambio
    if (order.status === 'finalized') {
      return [];
    }

    const availableStatuses = Object.keys(this.traduceStatus).filter(
      (s) => s !== order.status,
    ) as StatusKey[];

    if (!this.authService.isAdmin()) {
      return availableStatuses.filter((s) => s !== 'confirmed');
    }

    return availableStatuses;
  }

  onCustomerSelected(customer: any) {
    console.log(customer);

    if (customer) {
      this.filterForm.patchValue({ customerCode: customer.code });
      this.applyFilters();
    } else {
      this.filterForm.patchValue({ customerCode: '' });
      this.applyFilters();
    }
  }

  toggleFilters() {
    this.showFilters = !this.showFilters;
  }

  goToDetail(order: any) {
    this.router.navigate([`orders/${order.id}`]);
  }

  goToModify(order: any) {
    if (order.status !== 'pending') {
      this.alert.showAlert(
        `No se puede modificar un pedido en estado "${this.traduceStatus[order.status as StatusKey]}"`,
        'error',
      );
      return;
    }
    this.router.navigate([`orders/${order.id}/modify`]);
  }

  changeStatus(status: StatusKey, orderId: number) {
    // Buscar la orden actual para verificar su estado
    const currentOrder = this.filterOrders?.find(
      (order) => order.id === orderId,
    );

    // Impedir cambiar estado de órdenes ya finalizadas
    if (currentOrder && currentOrder.status === 'finalized') {
      this.alert.showAlert(
        `No se puede cambiar el estado de una orden en estado "${this.traduceStatus['finalized']}"`,
        'error',
      );
      return;
    }

    this.ordersService
      .updateOrder(status, orderId)
      .pipe(
        catchError((err) => {
          console.log(err);

          this.alert.showAlert(err.error.message, 'error');
          return of(null);
        }),
      )
      .subscribe((res: any) => {
        if (!res) return;

        this.filterOrders = this.filterOrders?.map((order) => {
          if (order.id === orderId) {
            return { ...order, status };
          }
          return order;
        });

        this.alert.showAlert(
          `Estado de la orden actualizado a ${this.getTranslatedStatus(status)}`,
          'success',
        );

        this.fetchOrders(this.filterForm.value, this.paginator.page);
      });
  }

  getTranslatedStatus(status: StatusKey): string {
    return this.traduceStatus[status];
  }

  goToPage(page: number) {
    if (
      page < 1 ||
      page > this.paginator.totalPages ||
      page === this.paginator.page
    )
      return;
    this.fetchOrders(this.filterForm.value, page);
    this.scrollContainer.nativeElement.scrollTop = 0;
  }

  nextPage() {
    if (this.paginator.page < this.paginator.totalPages) {
      this.goToPage(this.paginator.page + 1);
    }
  }

  prevPage() {
    if (this.paginator.page > 1) {
      this.goToPage(this.paginator.page - 1);
    }
  }

  getPageNumbers(): number[] {
    const total = this.paginator.totalPages;
    const pages: number[] = [];

    for (let i = 1; i <= total; i++) {
      pages.push(i);
    }

    return pages;
  }

  applyFilters() {
    const filters: any = {};
    const formValue = this.filterForm.value;

    if (formValue.orderId && formValue.orderId.toString().trim()) {
      filters.search = formValue.orderId.toString().trim();
    }

    if (formValue.status && formValue.status !== '') {
      filters.status = formValue.status;
    }

    if (formValue.dateFrom) {
      filters.dateFrom = formValue.dateFrom;
    }

    if (formValue.dateTo) {
      filters.dateTo = formValue.dateTo;
    }

    if (formValue.sellerCode) {
      filters.sellerCode = formValue.sellerCode;
    }

    if (formValue.customerCode) {
      filters.customer_code = formValue.customerCode;
    }

    this.fetchOrders(filters, 1);
  }

  resetFilters() {
    this.filterForm.reset({
      search: '',
      orderId: '',
      status: '',
      dateFrom: '',
      dateTo: '',
      sellerCode: '',
      customerCode: '',
    });
    this.fetchOrders({}, 1);
  }
}
