import { Component, OnDestroy } from '@angular/core';
import { CustomerDetailsComponent } from '../customer-details/customer-details.component';
import { RouterLink, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { BalanceService } from '../../services/balance.service';

@Component({
  selector: 'app-main-balances',
  imports: [CustomerDetailsComponent, RouterModule, RouterLink, CommonModule],
  templateUrl: './main-balances.component.html',
  styleUrl: './main-balances.component.scss'
})
export class MainBalancesComponent implements OnDestroy {


  constructor(private balanceService: BalanceService) {

  }

  ngOnDestroy(): void {
    this.balanceService.totalBalance.set(0)
    this.balanceService.lastReceiptDate.set('')
  }


}
