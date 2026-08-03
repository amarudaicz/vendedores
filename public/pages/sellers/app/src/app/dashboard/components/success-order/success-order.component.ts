import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-success-order',
  imports: [],
  templateUrl: './success-order.component.html',
  styleUrl: './success-order.component.scss'
})
export class SuccessOrderComponent {

  @Input() orderId!: number;
  @Output() reset: EventEmitter<any> = new EventEmitter();

  constructor() { }

  newOrder() {
    this.reset.emit();
  }

}
