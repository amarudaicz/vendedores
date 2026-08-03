import { Component, OnDestroy } from '@angular/core';
import { BalanceService } from '../../services/balance.service';
import { NgxSkeletonLoaderModule } from 'ngx-skeleton-loader';
import { CommonModule, CurrencyPipe } from '@angular/common';
import { ProductsService } from '../../../shared/services/products/products.service';

@Component({
  selector: 'app-customer-balance',
  imports: [NgxSkeletonLoaderModule, CurrencyPipe, CommonModule],
  templateUrl: './customer-balance.component.html',
  styleUrl: './customer-balance.component.scss',
})
export class CustomerBalanceComponent {
  receipts: any[] | undefined = undefined;
  filtredReceipts: any[] | undefined = undefined;
  dolar: number | null = null;

  constructor(
    public balanceService: BalanceService,
    private productService: ProductsService
  ) {
    this.balanceService.getPayments().subscribe((res) => {
      this.receipts = res.data.receipts;
      this.filtredReceipts = res.data.receipts;
      this.balanceService.totalBalance.set(res.data.total_balance);
      this.balanceService.lastReceiptDate.set(
        res.data.receipts[0]?.date_receipt
      );
      this.balanceService.receipts.set(res.data.receipts);
    });

    this.productService.dolar$.subscribe((dolar) => {
      this.dolar = dolar;
    });
  }

  getTotal(
    field:
      | 'subtotal_receipt'
      | 'iva_receipt'
      | 'total_receipt'
      | 'balance_receipt'
  ): number {
    return (
      this.balanceService.receipts()?.reduce((acc, curr) => {
        return acc + parseFloat(curr[field] || '0');
      }, 0) || 0
    );
  }
}
