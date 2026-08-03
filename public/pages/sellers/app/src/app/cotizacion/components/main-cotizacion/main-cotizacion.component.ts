import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { CotizacionService } from '../../services/cotizacion.service';

@Component({
  selector: 'app-main-cotizacion',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './main-cotizacion.component.html',
  styleUrl: './main-cotizacion.component.scss',
})
export class MainCotizacionComponent implements OnInit {
  dolarForm: FormGroup;
  isLoading = false;
  isSaving = false;
  errorMessage: string | null = null;
  successMessage: string | null = null;

  constructor(
    private fb: FormBuilder,
    private cotizacionService: CotizacionService
  ) {
    this.dolarForm = this.fb.group({
      dolar: [0, [Validators.required, Validators.min(0.01)]],
    });
  }

  ngOnInit(): void {
    this.loadCurrentRate();
  }

  loadCurrentRate(): void {
    this.isLoading = true;
    this.errorMessage = null;
    this.successMessage = null;

    this.cotizacionService.getDolar().subscribe({
      next: (rate) => {
        this.dolarForm.patchValue({ dolar: rate });
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Error loading dollar rate:', err);
        this.errorMessage = 'Error al cargar la cotización del dólar';
        this.isLoading = false;
      },
    });
  }

  save(): void {
    if (this.dolarForm.invalid) {
      this.markFormGroupTouched(this.dolarForm);
      return;
    }

    const rate = this.dolarForm.value.dolar;
    this.isSaving = true;
    this.errorMessage = null;
    this.successMessage = null;

    this.cotizacionService.updateDolar(rate).subscribe({
      next: (res) => {
        this.successMessage = 'Cotización actualizada correctamente';
        this.isSaving = false;
        setTimeout(() => {
          this.successMessage = null;
        }, 3000);
      },
      error: (err) => {
        console.error('Error updating dollar rate:', err);
        this.errorMessage = 'Error al actualizar la cotización del dólar';
        this.isSaving = false;
      },
    });
  }

  private markFormGroupTouched(formGroup: FormGroup): void {
    Object.values(formGroup.controls).forEach((control) => {
      control.markAsTouched();
      if (control instanceof FormGroup) {
        this.markFormGroupTouched(control);
      }
    });
  }
}
