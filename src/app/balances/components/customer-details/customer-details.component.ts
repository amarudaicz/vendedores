import { Component } from '@angular/core';
import { Customer } from '../../../interfaces/customer';
import { CustomersService } from '../../../clients/services/customers.service';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../../auth/services/auth.service';
import { BalanceService } from '../../services/balance.service';
import { CurrencyPipe } from '@angular/common';

@Component({
  selector: 'app-customer-details',
  imports: [RouterLink, CurrencyPipe],
  templateUrl: './customer-details.component.html',
  styleUrl: './customer-details.component.scss'
})
export class CustomerDetailsComponent {

  customer: Customer | null = null
  seller: any
  total_balance:number = 0

  constructor(private customerService: CustomersService, private authService: AuthService, private router: Router, public balanceService:BalanceService) {
    if (!this.customerService.customer()) {
      this.router.navigate(['clients'])
      return
    }
    
    this.customer = this.customerService.customer();
    this.authService.getSeller().subscribe((seller: any) => this.seller = seller)
  }

}
