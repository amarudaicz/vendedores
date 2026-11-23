import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import AlertService from '../../../shared/services/alert/alert.service';
import { AuthService } from '../../services/auth.service';
import { catchError, of } from 'rxjs';
import { NgClass, NgIf } from '@angular/common';

@Component({
  selector: 'app-reset-password',
  imports: [NgIf,NgClass,FormsModule],
  templateUrl: './reset-password.component.html',
  styleUrl: './reset-password.component.scss'
})
export class ResetPasswordComponent {
  currentPassword: string = '';
  newPassword: string = '';
  confirmPassword: string = '';

  isLengthValid: boolean = false;
  hasSpecialChar: boolean = false;
  hasNumber: boolean = false;
  hasUpperCase: boolean = false;

  constructor(private alert:AlertService, private auth:AuthService){

  }
 
  onSubmit() {
    if (!this.newPassword || this.newPassword !== this.confirmPassword) {
      this.alert.showAlert('Las contraseñas no coinciden.', 'error')
      return;
    }

    if (!this.isLengthValid || !this.hasSpecialChar || !this.hasNumber || !this.hasUpperCase) {
      this.alert.showAlert('La contraseña no cumple con todos los requisitos.', 'error');
      return;
    }

    const data={
      currentPassword:this.currentPassword,
      newPassword:this.newPassword
    }
    this.auth.resetPassword(data).pipe(
      catchError((err) => {
        console.log(err);
        this.alert.showAlert(err.error.message, 'error')
        return of(false)
      }),
    ).subscribe((res:any) => {
      if (!res) return
      this.alert.showAlert('Contraseña actualizada correctamente.', 'success')
      this.currentPassword = '';
      this.newPassword = '';
      this.confirmPassword = '';
      this.resetValidation();
    })
  }

  validatePassword() {
    const specialChars = /[!@#$%^&*(),.?":{}|<>]/;
    const numbers = /[0-9]/;
    const upperCase = /[A-Z]/;

    this.isLengthValid = this.newPassword.length >= 8;
    this.hasSpecialChar = specialChars.test(this.newPassword);
    this.hasNumber = numbers.test(this.newPassword);
    this.hasUpperCase = upperCase.test(this.newPassword);
  }

  resetValidation() {
    this.isLengthValid = false;
    this.hasSpecialChar = false;
    this.hasNumber = false;
    this.hasUpperCase = false;
  }
}
