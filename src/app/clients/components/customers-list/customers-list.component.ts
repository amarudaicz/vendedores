import {
  AfterViewInit,
  Component,
  ElementRef,
  OnInit,
  ViewChild,
} from '@angular/core';
import { Customer } from '../../../interfaces/customer';
import { CommonModule } from '@angular/common';
import { debounceTime, distinctUntilChanged, filter, finalize, fromEvent, map } from 'rxjs';
import { NgxSkeletonLoaderModule } from 'ngx-skeleton-loader';
import { FormBuilder, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { Seller } from '../../../interfaces/seller';
import { CustomersService } from '../../services/customers.service';
import { Router } from '@angular/router';
import { AuthService } from '../../../auth/services/auth.service';
import { SellersService } from '../../../shared/services/sellers/sellers.service';

@Component({
  selector: 'app-customers-list',
  imports: [CommonModule, NgxSkeletonLoaderModule, ReactiveFormsModule],
  templateUrl: './customers-list.component.html',
  styleUrl: './customers-list.component.scss',
})
export class CustomersListComponent implements OnInit {
  customers?: Customer[];
  loading: boolean = false;
  seller: Seller | null = null;
  sellers: Seller[] = [];
  filterForm: FormGroup;

  constructor(
    private customerService: CustomersService,
    private router: Router,
    private authService: AuthService,
    private sellerService: SellersService,
    private fb: FormBuilder
  ) {
    this.filterForm = this.fb.group({
      search: [''],
      sellerCode: ['']
    });

    this.authService.getSeller().subscribe((seller) => {
      this.seller = seller;
      if (this.seller) {
        this.fetchCustomers();
      }
    });

    this.sellerService.getSellers().subscribe((res) => {
      this.sellers = res;
    });
  }

  ngOnInit(): void {
    this.filterForm.valueChanges.pipe(
      debounceTime(400),
      distinctUntilChanged((prev, curr) => JSON.stringify(prev) === JSON.stringify(curr))
    ).subscribe(() => {
      this.fetchCustomers();
    });
  }

  fetchCustomers() {
    this.loading = true;
    const { search, sellerCode } = this.filterForm.value;
    
    this.customerService
      .getCustomers(search, sellerCode)
      .pipe(finalize(() => (this.loading = false)))
      .subscribe((res) => {
        this.customers = res.data.customers;
      });
  }

  selectCustomer(customer: Customer) {
    this.customerService.customer.set(customer);
    this.router.navigate(['client/balance']);
  }
}
