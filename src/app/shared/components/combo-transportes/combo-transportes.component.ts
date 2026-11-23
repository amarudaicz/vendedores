import { CommonModule } from '@angular/common';
import { Component, Input, OnInit } from '@angular/core';
import { FormGroup, ReactiveFormsModule } from '@angular/forms';
import { SelectModule } from 'primeng/select';
import { Transporte } from '../../../interfaces/transporte.interface';
import { TransportesService } from '../../services/transportes/transportes.service';

@Component({
  selector: 'app-combo-transportes',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, SelectModule],
  templateUrl: './combo-transportes.component.html',
  styleUrl: './combo-transportes.component.scss'
})
export class ComboTransportesComponent implements OnInit {

  @Input() form!: FormGroup;
  @Input() controlName: string = 'idTransporte';

  transportes: Transporte[] = [];
  loading = true;

  constructor(private transportesService: TransportesService) {}

  ngOnInit(): void {
    this.transportesService.getTransportes().subscribe(list => {
      this.transportes = list;
      this.loading = false;
    });
  }
}
