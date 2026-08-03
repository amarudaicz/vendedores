import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgClass, NgIf } from '@angular/common';
import { OrderStats } from '../../interface/OrderInterface';
import { NgxSkeletonLoaderModule } from 'ngx-skeleton-loader';
import { OrdersService } from '../../../shared/services/orders/orders.service';

@Component({
    selector: 'app-stats-orders',
    imports: [NgClass, NgxSkeletonLoaderModule, NgIf],
    templateUrl: './stats-orders.component.html',
    styleUrl: './stats-orders.component.scss'
})

export class StatsOrdersComponent implements OnInit {

  stats?:OrderStats

  constructor(
    private orderService: OrdersService, 
    private cdr: ChangeDetectorRef
  ) { }

  ngOnInit() {
    this.orderService.stats$.subscribe((stats) => {
      this.stats = stats;
      this.cdr.detectChanges();
    });
  }


  isNegative(n:number){
    return n < 0
  }

}


