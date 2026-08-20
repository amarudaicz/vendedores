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
import { ConfirmDialogModule } from 'primeng/confirmdialog';
import { ConfirmationService } from 'primeng/api';
import AlertService from '../../../shared/services/alert/alert.service';
import { OrdersService } from '../../../shared/services/orders/orders.service';
import { SellersService } from '../../../shared/services/sellers/sellers.service';
import { AuthService } from '../../../auth/services/auth.service';
import { SearchClientComponent } from '../../../shared/components/search-client/search-client.component';
import { Seller } from '../../../interfaces/seller';
import { Order, StatusKey, STATUS_FLOW, STATUS_LABELS } from '../../../interfaces/Order';


@Component({
  selector: 'app-orders-list',
  imports: [
    CommonModule,
    NgxSkeletonLoaderModule,
    ReactiveFormsModule,
    SearchClientComponent,
    ConfirmDialogModule,
  ],
  providers: [ConfirmationService],
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
    private confirmationService: ConfirmationService,
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

  readonly traduceStatus = STATUS_LABELS;


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
      return availableStatuses.filter((s) => s === 'confirmed' || s === 'pending');
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

  deleteOrder(order: any) {
    if (order.status !== 'pending') {
      this.alert.showAlert(
        `Solo se pueden eliminar pedidos en estado "Pendiente"`,
        'error',
      );
      return;
    }

    const confirmed = window.confirm(
      `¿Seguro que querés eliminar la orden #${order.id}? Esta acción no se puede deshacer.`,
    );

    if (!confirmed) return;

    this.ordersService
      .deleteOrder(order.id)
      .pipe(
        catchError((err) => {
          console.log(err);
          this.alert.showAlert(
            err.error?.message || 'No se pudo eliminar la orden',
            'error',
          );
          return of(null);
        }),
      )
      .subscribe((res) => {
        if (!res) return;

        this.filterOrders = this.filterOrders?.filter(
          (o) => o.id !== order.id,
        );

        this.alert.showAlert(
          `Orden #${order.id} eliminada correctamente`,
          'success',
        );

        this.fetchOrders(this.filterForm.value, this.paginator.page);
      });
  }

  changeStatus(status: StatusKey, orderId: number) {
    // 'En proceso' es un estado automático — nunca seleccionable manualmente
    if (status === 'in_progress') {
      this.alert.showAlert(
        'Este estado es automático, no puede ser seleccionado.',
        'warning',
      );
      return;
    }

    const currentOrder = this.filterOrders?.find(
      (order) => order.id === orderId,
    );

    const currentStatus = currentOrder?.status as StatusKey;

    // Validar que la transición esté permitida por el STATUS_FLOW
    const allowedTransitions = STATUS_FLOW[currentStatus] ?? [];
    if (!allowedTransitions.includes(status)) {
      const fromLabel = this.traduceStatus[currentStatus] ?? currentStatus;
      const toLabel   = this.traduceStatus[status] ?? status;
      this.alert.showAlert(
        `Transición no permitida: no se puede pasar de "${fromLabel}" a "${toLabel}".`,
        'warning',
      );
      return;
    }


    // Configuración base compartida
    const baseConfig = {
      acceptLabel: 'Aceptar',
      rejectLabel: 'Cancelar',
      acceptButtonStyleClass: 'p-button-primary',
      rejectButtonStyleClass: 'p-button-text',
      dismissableMask: true,
      defaultFocus: 'accept' as const,
      accept: () => this.executeStatusChange(status, orderId),
    };

    // PENDIENTE → CONFIRMADO
    if (currentStatus === 'pending' && status === 'confirmed') {
      this.confirmationService.confirm({
        ...baseConfig,
        header: 'Confirmar envío al Sistema de Gestión',
        message: 'Confirmá el envío de la <strong>Nota de Pedido</strong> al Sistema de Gestión.',
        icon: 'pi pi-send',
      });
      return;
    }

    // EN PROCESO → ENVIADO
    if (currentStatus === 'in_progress' && status === 'finalized') {
      this.confirmationService.confirm({
        ...baseConfig,
        header: 'PEDIDO ENVIADO',
        message: 'Se enviará una <strong>notificación al cliente</strong> informando que su pedido está en camino.',
        icon: 'pi pi-truck',
      });
      return;
    }

    // Cualquier otra transición (ej: admin cambiando a un estado arbitrario)
    this.confirmationService.confirm({
      ...baseConfig,
      header: 'Confirmar cambio de estado',
      message: `¿Confirmás que querés cambiar el estado de la orden <strong>#${orderId}</strong> a <strong>${this.getTranslatedStatus(status)}</strong>?`,
      icon: 'pi pi-exclamation-triangle',
    });
  }

  private executeStatusChange(status: StatusKey, orderId: number) {
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
