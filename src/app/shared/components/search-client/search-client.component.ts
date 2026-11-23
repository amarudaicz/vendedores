import { CommonModule } from '@angular/common';
import { AfterViewInit, Component, ElementRef, EventEmitter, Input, Output, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { CustomersService } from '../../services/customers/customers.service';
import { catchError, debounceTime, filter, fromEvent, map, of, switchMap, tap } from 'rxjs';
import { AuthService } from '../../../auth/services/auth.service';
import { Router } from '@angular/router';

@Component({
    selector: 'app-search-client',
    imports: [ReactiveFormsModule, CommonModule],
    templateUrl: './search-client.component.html',
    styleUrl: './search-client.component.scss'
})
export class SearchClientComponent implements AfterViewInit {

  @Input() form!: FormGroup;
  @Input() labelClass: string = "";
  @Input() required: boolean = true;
  suggestions: any[] | undefined = undefined;
  customer: any


  @Output() customerEmitter = new EventEmitter();
  @ViewChild('clientInput', { static: false }) clientInput!: ElementRef;
  seller_code: any
  constructor(private fb: FormBuilder, private customerService: CustomersService, private auth: AuthService, private router:Router) {
    this.auth.getSeller().subscribe((seller: any) => {
      if (!seller) return
      this.seller_code = seller.code
    })
    
  }

  ngAfterViewInit(): void {
    fromEvent(this.clientInput.nativeElement, 'input').pipe(
      debounceTime(300),
      map((event: any) => event.target.value),
      filter((query: string) => {
        return query.length ? true : (this.reset(), false);
      }),
      switchMap((query: string) => 
        this.customerService.getCustomers(query).pipe(
          catchError((err) => {
            console.error('Error en la solicitud:', err);
            return of({ data: { customers: [] } }); // Retorna un objeto vacío para no romper el flujo
          })
        )
      )
    ).subscribe((res: any) => {
      this.suggestions = res.data.customers || [];
      // this.customerEmitter.next(null); 
      this.customer = undefined;
    });
  }

  reset() {
    this.customerEmitter.emit(null);
    this.suggestions = undefined;
  }

  selectCustomer(customer: any) {
    this.customerEmitter.emit(customer);
    this.customer = customer
    this.form.patchValue({ search: customer.name })
    this.suggestions = undefined;
  }


  isSearchInvalid(): boolean {
    if (!this.required) return false;
    const searchControl = this.form.get('search');
    return ((searchControl?.invalid && searchControl?.touched) || (!this.customer && searchControl?.touched)) || false;
  }


}
