import { Component } from '@angular/core';
import { OrdersListComponent } from "../orders-list/orders-list.component";
import { StatsOrdersComponent } from "../stats-orders/stats-orders.component";

@Component({
    selector: 'app-main-orders',
    imports: [OrdersListComponent, StatsOrdersComponent],
    templateUrl: './main-orders.component.html',
    styleUrl: './main-orders.component.scss'
})
export class MainOrdersComponent {

}
