import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { AfterContentInit, AfterViewChecked, AfterViewInit, ChangeDetectorRef, Component, ElementRef, OnChanges, SimpleChanges, ViewChild } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms'
import { catchError, debounceTime, fromEvent, of, Subject, switchMap, tap } from 'rxjs';
import { CustomersService } from '../../../shared/services/customers/customers.service';
import { SearchClientComponent } from "../../../shared/components/search-client/search-client.component";
import {AlertService} from '../../../shared/services/alert/alert.service';
import { ItemsListComponent } from "../items-list/items-list.component";
import { SuccessOrderComponent } from '../success-order/success-order.component';
import { OrdersService } from '../../../shared/services/orders/orders.service';
import { Customer } from '../../../interfaces/customer';
import { ProductsService } from '../../../shared/services/products/products.service';
import { globalConfig } from '../../../global-config';

@Component({
    selector: 'app-new-order',
    imports: [CommonModule, ReactiveFormsModule, SearchClientComponent, ItemsListComponent, SuccessOrderComponent],
    providers: [HttpClient],
    templateUrl: './new-order.component.html',
    styleUrl: './new-order.component.scss'
})
export class NewOrderComponent {

  formNewOrder: FormGroup
  prevCustomer:any
  items:any = [];
  loadingForm:boolean = false;
  dolar?:number;
  orderId: number | null = null;
  globalConfig = globalConfig;

  constructor(private fb: FormBuilder, private ordersService: OrdersService, private alertService:AlertService, private productService:ProductsService) {

    this.formNewOrder = fb.group({
      customer: [null, [Validators.required]],
      moneda: [null, []],
      usdValue: [null],
      note: [null, ],
      paymentMethod: [null, []],
      items: fb.array([]),
      discount:[0],
      search:[null, []]
    })

    this.productService.getDolar();
    
    this.productService.dolar$.subscribe(res=>{
      this.dolar = res
    })

  }

  submitForm(){
    console.log(this.formNewOrder);
    
    if (!this.isValidForm()) {
      let errorMessage = '';
    
      if (!this.formNewOrder.valid) {
        errorMessage = 'Por favor complete todos los campos correctamente.';
      } else if (!this.getCustomer()) {
        errorMessage = 'Debe seleccionar un cliente antes de continuar.';
      } else if (this.items.length === 0) {
        errorMessage = 'Debe agregar al menos un producto al pedido.';
      }
    
      this.alertService.showAlert(errorMessage, 'error');
      this.formNewOrder.markAllAsTouched();
      return;
    }

    this.loadingForm = true;

    const order = {
      ...this.formNewOrder.value,
      items:this.items
    }

    this.ordersService.postOrder(order).pipe(
      catchError(err => {
        this.alertService.showAlert('Error al crear la orden: ' + (err.error?.message || err.message || 'Error desconocido'), 'error');
        this.loadingForm = false;
        return of(err);
      })
    ).subscribe(res=>{
      
      if (!res.data || !res.data.orderId) {
        this.alertService.showAlert('Error al crear la orden: ' + (res.message || 'Respuesta inválida'), 'error');
        this.loadingForm = false;
        return
      }

      this.orderId = res.data.orderId; // Set orderId to show success screen
      this.loadingForm = false;
    })

  }

  setItems(itemsFromChild:any){
    this.items = itemsFromChild
  }

  isValidForm(): boolean {
    return this.formNewOrder.valid && this.getCustomer() && this.items.length > 0;
  }

  setCustomer(customer:any){
    this.formNewOrder.patchValue({customer:customer});
  }

  getCustomer():Customer{
    return this.formNewOrder.get('customer')?.value
  }

  preventEnter(event:any) {
    event.preventDefault();
  }

  resetForm() {
    this.orderId = null;
    this.formNewOrder.reset();
    (this.formNewOrder.get('items') as FormArray).clear();
    this.items = [];
    this.formNewOrder.patchValue({discount: 0});
  }

}
