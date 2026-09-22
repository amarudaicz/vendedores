import { CommonModule } from '@angular/common';
import { Component, Input } from '@angular/core';
import { FormGroup, ReactiveFormsModule } from '@angular/forms';
import { SelectModule } from 'primeng/select';

export interface MetodoEnvioOption {
  label: string;
  value: string;
}

@Component({
  selector: 'app-combo-metodo-envio',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, SelectModule],
  templateUrl: './combo-metodo-envio.component.html',
  styleUrl: './combo-metodo-envio.component.scss'
})
export class ComboMetodoEnvioComponent {

  @Input() form!: FormGroup;
  @Input() controlName: string = 'deliveryMethod';

  options: MetodoEnvioOption[] = [
    { label: 'Envío a domicilio', value: 'Envío a domicilio' },
    { label: 'Retiro en depósito/local', value: 'Retiro en Local' }
  ];
}
