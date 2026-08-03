import {
  Component,
  ElementRef,
  Input,
  OnDestroy,
  ViewChild,
} from '@angular/core';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { CommonModule, Location } from '@angular/common';
import { PdfService } from '../../../shared/services/pdf/pdf.service';
import { catchError, finalize, of } from 'rxjs';
import AlertService from '../../../shared/services/alert/alert.service';
import { NgxSkeletonLoaderModule } from 'ngx-skeleton-loader';
import { OrdersService } from '../../../shared/services/orders/orders.service';
import { ProductsService } from '../../../shared/services/products/products.service';
import { globalConfig } from '../../../global-config';

@Component({
  selector: 'app-order-detail',
  imports: [RouterModule, CommonModule, NgxSkeletonLoaderModule],
  templateUrl: './order-detail.component.html',
  styleUrl: './order-detail.component.scss',
})
export class OrderDetailComponent {
  id: any;
  order: any;
  customer: any;
  items: any;
  total: any;
  guest: any;
  loading: boolean = true;
  cotizacion: number = 0;
  globalConfig = globalConfig;
  @ViewChild('orderContent', { static: true }) orderContent!: ElementRef;

  constructor(
    private location: Location,
    private activatedRoute: ActivatedRoute,
    private ordersService: OrdersService,
    private pdf: PdfService,
    private productsService: ProductsService
  ) {
    this.id = this.activatedRoute.snapshot.params['id'];
    this.loading = true;
    this.ordersService
      .getOrder(this.id)
      .pipe(
        finalize(() => {
          this.loading = false;
        }),
      )
      .subscribe((res: any) => {
        console.log(res);
        
        this.order = res.data.order;
        this.customer = res.data.customer;
        this.items = res.data.items;
        this.guest = res.data.guest;
        this.total = res.data.items
          .map((item: any) => item.price * item.quantity)
          .reduce((a: any, b: any) => a + b, 0);
        this.cotizacion = res.data.order.cotizacion
        console.log(this.cotizacion);
        
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
      .getOrderCsv(this.id)
      .pipe(
        catchError((err) => {
          return of(err);
        }),
      )
      .subscribe((res: any) => {
        // Decodificar el CSV en Base64
        const decodedCsv = atob(res.data.csv.split(',')[1]); // Extraer y decodificar la parte Base64

        // Crear un Blob con el contenido decodificado
        const blob = new Blob([decodedCsv], {
          type: 'text/csv;charset=utf-8;',
        });
        const url = window.URL.createObjectURL(blob);

        // Crear y simular clic en un enlace para descargar el archivo
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `${this.id}.csv`);
        document.body.appendChild(link); // Necesario para Firefox
        link.click();
        document.body.removeChild(link); // Limpiar el enlace del DOM
      });
  }

  get idOrden() {
    return this.order?.id?.toString().padStart(8, '0') || '';
  }
}
