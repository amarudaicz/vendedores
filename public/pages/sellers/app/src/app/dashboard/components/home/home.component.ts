import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { NewOrderComponent } from "../new-order/new-order.component";

@Component({
    selector: 'app-home',
    imports: [CommonModule, NewOrderComponent],
    templateUrl: './home.component.html',
    styleUrl: './home.component.scss'
})

export class HomeComponent {




}
