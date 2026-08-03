import { Component, OnInit, HostListener } from '@angular/core';
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
export class OrderModifyComponent implements OnInit {
  orderId: number = 0;
  order: any = null;
  customer: any = null;
  orderItems: any[] = [];
  allProducts: any[] = [];
  filteredProducts: any[] = [];
  productSearchTerm: string = '';
  showProductDropdown: boolean = false;
  loading = true;
  saving = false;

  newItemForm!: FormGroup;
  originalItems: any[] = [];

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
    this.activatedRoute.params.subscribe((params) => {
      this.orderId = parseInt(params['id']);
      this.loadOrder();
    });
  }

  loadAllProducts() {
    this.ordersService.searchProducts('', this.customer.priceList).subscribe(
      (res: any) => {
        this.allProducts = res.data.products || [];
        this.filteredProducts = this.allProducts;
      },
      (error: any) => {
        // Handle error silently
      },
    );
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

    const term = searchTerm.toLowerCase();
    this.filteredProducts = this.allProducts.filter((product) => {
      const name = (product.name || '').toLowerCase();
      const description = (product.description || '').toLowerCase();
      const code = (
        product.code ||
        product.product_code ||
        product.productCode ||
        ''
      )
        .toString()
        .toLowerCase();
      return (
        name.includes(term) || description.includes(term) || code.includes(term)
      );
    });
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
    this.ordersService.getOrder(this.orderId).subscribe(
      (res: any) => {
        this.order = res.data.order;
        this.customer = res.data.customer || res.data.guest;

        console.log(this.order);

        // Cargar productos DESPUÉS de tener el customer
        this.loadAllProducts();

        // Mapear productCode a product_code en cada item
        this.orderItems = res.data.items.map((item: any) => ({
          ...item,
          product_code: item.productCode || item.product_code,
        }));

        this.loading = false;
      },
      (error: any) => {
        this.alert.showAlert('Error al cargar la orden', 'error');
        this.loading = false;
      },
    );
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

    const modificationData = {
      items: this.orderItems,
      originalItems: this.originalItems,
    };

    this.ordersService
      .updateOrderItems(this.orderId, modificationData)
      .subscribe(
        (res: any) => {
          this.alert.showAlert('Orden modificada exitosamente', 'info');
          this.saving = false;
          setTimeout(() => this.router.navigate(['/orders']), 1500);
        },
        (error: any) => {
          this.alert.showAlert('Error al guardar los cambios', 'error');
          this.saving = false;
        },
      );
  }

  cancel() {
    this.router.navigate(['/orders']);
  }

  getStatusLabel(status: string): string {
    const labels: { [key: string]: string } = {
      pending: 'Pendiente',
      finalized: 'Completada',
      not_realized: 'No Concretada',
    };
    return labels[status] || status;
  }

  getTotalItems(): number {
    return this.orderItems.length;
  }
}
