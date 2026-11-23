import {
  Component,
  ElementRef,
  ViewChild,
} from '@angular/core';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { CommonModule, Location } from '@angular/common';
import { PdfService } from '../../../shared/services/pdf/pdf.service';
import { catchError, EMPTY, finalize } from 'rxjs';
import AlertService from '../../../shared/services/alert/alert.service';
import { NgxSkeletonLoaderModule } from 'ngx-skeleton-loader';
import { OrdersService } from '../../../shared/services/orders/orders.service';
import { globalConfig } from '../../../global-config';
import { OrderDetail, OrderItem } from '../../../interfaces/Order';

@Component({
  selector: 'app-order-detail',
  imports: [RouterModule, CommonModule, NgxSkeletonLoaderModule],
  templateUrl: './order-detail.component.html',
  styleUrl: './order-detail.component.scss',
})
export class OrderDetailComponent {
  id: any;
  order: OrderDetail | null = null;
  customer: any;
  items: OrderItem[] = [];
  total: number = 0;
  guest: any;
  loading: boolean = true;
  cotizacion: number = 1;
  globalConfig = globalConfig;
  @ViewChild('orderContent', { static: true }) orderContent!: ElementRef;

  constructor(
    private location: Location,
    private activatedRoute: ActivatedRoute,
    private ordersService: OrdersService,
    private pdf: PdfService,
    private alertService: AlertService,
  ) {
    this.id = this.activatedRoute.snapshot.params['id'];
    this.loading = true;
    this.ordersService
      .getOrderById(this.id)
      .pipe(
        finalize(() => {
          this.loading = false;
        }),
      )
      .subscribe({
        next: (res) => {
          this.order = res.data;
          this.customer = res.data.customer;
          this.guest = res.data.guest;
          this.items = res.data.items || [];
          this.total = this.items
            .map((item) => item.price * item.quantity)
            .reduce((a, b) => a + b, 0);
          this.cotizacion = res.data.cotizacion ?? 1;
        },
        error: (err) => {
          this.alertService.showAlert(
            err?.error?.message || 'Error al cargar el detalle de la orden',
            'error',
          );
        },
      });
  }

  downloadPdf() {
    this.pdf.printPDF('order-content', this.id);
  }

  back() {
    this.location.back();
  }

  downloadCsv() {
    this.ordersService
      .getCsv(this.id)
      .pipe(
        catchError((err) => {
          this.alertService.showAlert(
            err?.error?.message || 'No se pudo descargar el CSV',
            'error',
          );
          return EMPTY;
        }),
      )
      .subscribe((res) => {
        if (!res?.data?.csv) {
          this.alertService.showAlert('CSV no disponible', 'error');
          return;
        }
        const csvPart = res.data.csv.includes(',')
          ? res.data.csv.split(',')[1]
          : res.data.csv;
        const decodedCsv = atob(csvPart);

        const blob = new Blob([decodedCsv], {
          type: 'text/csv;charset=utf-8;',
        });
        const url = window.URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `${this.id}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
      });
  }

  get idOrden() {
    return this.order?.id?.toString().padStart(8, '0') || '';
  }

  get customerDiscountPercent(): number {
    return Number(this.customer?.descuento || 0);
  }

  get subtotalArs(): number {
    return (this.total || 0) * (this.globalConfig.cotizacion ? this.cotizacion : 1);
  }

  get discountAmountArs(): number {
    return this.subtotalArs * (this.customerDiscountPercent / 100);
  }

  get totalWithDiscountArs(): number {
    return this.subtotalArs - this.discountAmountArs;
  }

  get discountAmountUsd(): number {
    return (this.total || 0) * (this.customerDiscountPercent / 100);
  }

  get totalWithDiscountUsd(): number {
    return (this.total || 0) - this.discountAmountUsd;
  }
}
