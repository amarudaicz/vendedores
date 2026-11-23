import { Component, OnInit, OnDestroy, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormsModule,
  ReactiveFormsModule,
  FormBuilder,
  FormGroup,
  Validators,
} from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { OrdersService } from '../../../shared/services/orders/orders.service';
import AlertService from '../../../shared/services/alert/alert.service';
import { NgxSkeletonLoaderModule } from 'ngx-skeleton-loader';
import { Subject, debounceTime, distinctUntilChanged, takeUntil } from 'rxjs';
import {
  OrderDetail,
  STATUS_LABELS,
  StatusKey,
} from '../../../interfaces/Order';

@Component({
  selector: 'app-order-modify',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    NgxSkeletonLoaderModule,
  ],
  templateUrl: './order-modify.component.html',
  styleUrl: './order-modify.component.scss',
})
export class OrderModifyComponent implements OnInit, OnDestroy {
  orderId: number = 0;
  order: OrderDetail | null = null;
  customer: any = null;
  guest: any = null;
  orderItems: any[] = [];
  allProducts: any[] = [];
  filteredProducts: any[] = [];
  productSearchTerm: string = '';
  showProductDropdown: boolean = false;
  loading = true;
  saving = false;

  newItemForm!: FormGroup;
  originalItems: any[] = [];
  onChangeSearch = new Subject<string>();
  private destroy$ = new Subject<void>();

  constructor(
    private activatedRoute: ActivatedRoute,
    private router: Router,
    private ordersService: OrdersService,
    private alert: AlertService,
    private fb: FormBuilder,
  ) {
    this.newItemForm = this.fb.group({
      productCode: ['', Validators.required],
      quantity: [1, [Validators.required, Validators.min(1)]],
    });
  }

  @HostListener('document:click', ['$event'])
  onDocumentClick(event: MouseEvent) {
    const target = event.target as HTMLElement;
    if (!target.closest('.product-search-container')) {
      this.showProductDropdown = false;
    }
  }

  ngOnInit() {
    this.activatedRoute.params
      .pipe(takeUntil(this.destroy$))
      .subscribe((params) => {
        this.orderId = parseInt(params['id'], 10);
        this.loadOrder();
      });

    this.onChangeSearch
      .pipe(debounceTime(500), distinctUntilChanged(), takeUntil(this.destroy$))
      .subscribe((query) => {
        if (!this.order) return;
        this.loadAllProducts(query);
      });
  }

  ngOnDestroy() {
    this.destroy$.next();
    this.destroy$.complete();
  }

  loadAllProducts(searchTerm?: string) {
    if (!this.order) return;
    const priceList = this.customer?.priceList ?? this.customer?.price_list ?? 1;
    this.ordersService
      .searchProducts(searchTerm || '', priceList)
      .subscribe({
        next: (res: any) => {
          this.allProducts = res.data.products || [];
          this.filteredProducts = this.allProducts;
        },
        error: () => {
          // Handle error silently
        },
      });
  }

  onProductInputFocus() {
    this.showProductDropdown = true;
    this.filteredProducts = this.allProducts;
  }

  filterProducts(searchTerm: string) {
    this.productSearchTerm = searchTerm;
    this.showProductDropdown = true;

    if (!searchTerm.trim()) {
      this.filteredProducts = this.allProducts;
      return;
    }  

    this.onChangeSearch.next(searchTerm)

    // this.loadAllProducts(searchTerm);
  }

  selectProduct(product: any) {
    const productCode =
      product.code || product.product_code || product.productCode;

    if (!productCode) {
      this.alert.showAlert('Error: producto sin código', 'error');
      return;
    }

    this.newItemForm.patchValue({ productCode });
    this.productSearchTerm = product.name || product.description;
    this.showProductDropdown = false;
  }

  loadOrder() {
    this.ordersService.getOrderById(this.orderId).subscribe({
      next: (res) => {
        this.order = res.data;
        this.customer = res.data.customer || null;
        this.guest = res.data.guest || null;

        // Cargar productos DESPUÉS de tener el customer
        this.loadAllProducts();

        // Mapear productCode a product_code en cada item
        this.orderItems = (res.data.items || []).map((item) => ({
          ...item,
          product_code: item.productCode || item.product_code,
        }));
        this.originalItems = [...this.orderItems];

        this.loading = false;
      },
      error: () => {
        this.alert.showAlert('Error al cargar la orden', 'error');
        this.loading = false;
      },
    });
  }

  getProductName(code: string): string {
    const item = this.orderItems.find((i) => i.product_code === code);
    return item ? item.description : code;
  }

  getProductPrice(code: string): number {
    const item = this.orderItems.find((i) => i.product_code === code);
    return item ? item.price : 0;
  }

  addItem() {
    if (this.newItemForm.invalid) {
      this.alert.showAlert('Debe seleccionar un producto y cantidad', 'error');
      return;
    }

    const productCode = this.newItemForm.get('productCode')?.value;
    const quantity = this.newItemForm.get('quantity')?.value;

    const product = this.allProducts.find((p) => p.code === productCode);

    if (!product) {
      this.alert.showAlert('Producto no encontrado', 'error');
      return;
    }

    const existingItem = this.orderItems.find(
      (i) => i.product_code === productCode,
    );
    console.log(existingItem);

    if (existingItem) {
      if (Number(existingItem.quantity) + Number(quantity) > product.stock) {
        this.alert.showAlert(
          'No hay stock suficiente para esta cantidad',
          'error',
        );
        return;
      }

      const newQuantity = Number(existingItem.quantity) + Number(quantity);

      existingItem.quantity = newQuantity;
      this.alert.showAlert('Cantidad actualizada', 'success');
      this.newItemForm.reset({ quantity: 1, productCode: '' });
      this.productSearchTerm = '';
      return;
    }

    if (this.newItemForm.get('quantity')?.value > product.stock) {
      this.alert.showAlert(
        'No hay stock suficiente para esta cantidad',
        'error',
      );
      return;
    }

    const newItem = {
      product_code: productCode,
      description: product.name || product.description,
      quantity: quantity,
      price: product.price,
      isNew: true,
    };

    this.orderItems.push(newItem);

    this.newItemForm.reset({ quantity: 1, productCode: '' });
    this.productSearchTerm = '';
    this.alert.showAlert('Artículo agregado', 'info');
  }

  removeItem(index: number) {
    this.orderItems.splice(index, 1);
  }

  updateQuantity(index: number, quantity: number) {
    if (quantity > 0) {
      this.orderItems[index].quantity = quantity;
    }
  }

  calculateTotal(): number {
    return this.orderItems.reduce(
      (sum, item) => sum + item.price * item.quantity,
      0,
    );
  }

  saveChanges() {
    if (this.orderItems.length === 0) {
      this.alert.showAlert(
        'Debe tener al menos un artículo en la orden',
        'error',
      );
      return;
    }

    this.saving = true;

    const itemsToUpdate = this.orderItems.map((item) => ({
      product_code: item.product_code || item.productCode,
      description: item.description,
      quantity: Number(item.quantity),
      price: Number(item.price),
    }));

    this.ordersService.updateOrderItems(this.orderId, itemsToUpdate).subscribe({
      next: () => {
        this.alert.showAlert('Orden modificada exitosamente', 'info');
        this.saving = false;
        setTimeout(() => this.router.navigate(['/orders']), 1500);
      },
      error: (err) => {
        this.alert.showAlert(
          err?.error?.message || 'Error al guardar los cambios',
          'error',
        );
        this.saving = false;
      },
    });
  }

  cancel() {
    this.router.navigate(['/orders']);
  }

  getStatusLabel(status: string): string {
    return STATUS_LABELS[status as StatusKey] || status;
  }

  getTotalItems(): number {
    return this.orderItems.length;
  }
}
