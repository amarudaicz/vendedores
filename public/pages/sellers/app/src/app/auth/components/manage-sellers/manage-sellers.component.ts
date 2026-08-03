import { Component, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  ReactiveFormsModule,
  FormBuilder,
  FormGroup,
  Validators,
} from '@angular/forms';
import { SellersService } from '../../../shared/services/sellers/sellers.service';
import { HttpClientModule } from '@angular/common/http';
import { Seller } from '../../../interfaces/seller';
import AlertService from '../../../shared/services/alert/alert.service';

@Component({
  selector: 'app-manage-sellers',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, HttpClientModule],
  providers: [SellersService],
  templateUrl: './manage-sellers.component.html',
  styles: [],
})
export class ManageSellersComponent implements OnInit {
  sellersService = inject(SellersService);
  fb = inject(FormBuilder);

  sellers: Seller[] = [];
  isVisible = false;
  isLoading = false;
  selectedSeller: any = null;

  passwordForm: FormGroup;

  constructor(private alertService:AlertService ) {
    this.passwordForm = this.fb.group(
      {
        newPassword: [
          '',
          [
            Validators.required,
            Validators.minLength(6),
            Validators.pattern(
              /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!"#$%&/()=?¡]).*$/
            ),
          ],
        ],
        confirmPassword: ['', [Validators.required]],
      },
      { validators: this.passwordMatchValidator },
    );
  }

  ngOnInit(): void {
    this.sellersService.getSellers().subscribe((res) => {
      this.sellers = res;
    });
  } 

  passwordMatchValidator(g: FormGroup) {
    return g.get('newPassword')?.value === g.get('confirmPassword')?.value
      ? null
      : { mismatch: true };
  }

  openPasswordModal(seller: any) {
    this.selectedSeller = seller;
    this.passwordForm.reset();
    this.showPassword = false;
    this.showConfirmPassword = false;
    this.isVisible = true;
  }

  showPassword = false;
  showConfirmPassword = false;

  togglePasswordVisibility(): void {
    this.showPassword = !this.showPassword;
  }

  toggleConfirmPasswordVisibility(): void {
    this.showConfirmPassword = !this.showConfirmPassword;
  }

  handleCancel() {
    this.isVisible = false;
    this.selectedSeller = null;
  }

  handleOk() {
    if (this.passwordForm.invalid) return;

    this.isLoading = true;
    const newPassword = this.passwordForm.get('newPassword')?.value;

    this.sellersService
      .updatePassword(newPassword, undefined, this.selectedSeller.id)
      .subscribe({
        next: () => {
          this.alertService.showAlert('Contraseña actualizada correctamente', 'success');
          this.isLoading = false;
          this.isVisible = false;
        },
        error: (err) => {
          this.isLoading = false;
        },
      });
  }
}
