import { Component } from '@angular/core';
import { SearchClientComponent } from "../../../shared/components/search-client/search-client.component";
import { FormBuilder, FormGroup } from '@angular/forms';
import { CommonModule, DatePipe, NgIf } from '@angular/common';
import { CustomersListComponent } from '../customers-list/customers-list.component';

@Component({
  selector: 'app-main-clients',
  imports: [CommonModule, CustomersListComponent],
  templateUrl: './main-clients.component.html',
  styleUrl: './main-clients.component.scss'
})
export class MainClientsComponent {
  form: FormGroup
  customer: any


  constructor(private fb: FormBuilder) {
    this.form = this.fb.group({
      search: ['']
    })
  }

  getCustomer(customer: any) {
    this.customer = customer;
  } 


}
