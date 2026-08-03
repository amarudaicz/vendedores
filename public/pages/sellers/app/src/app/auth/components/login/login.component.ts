import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Component, NgModule } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  NgModel,
  ReactiveFormsModule,
} from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { catchError, of } from 'rxjs';

@Component({
  selector: 'app-login',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.scss',
})
export class LoginComponent {
  form: FormGroup;
  error: any = null;
  loading = false;

  constructor(
    private auth: AuthService,
    private fb: FormBuilder,
    private router: Router
  ) {
    this.form = fb.group({
      email: [null],
      password: [null],
    });
  }

  login() {
    this.error = null;
    this.loading = true;
    this.auth
      .logIn(this.form.value)
      .pipe(
        catchError((res) => {
          this.error = res.error.message || 'Ocurrió un error';
          this.loading = false;
          return of(false);
        })
      )
      .subscribe((res: any) => {
        this.loading = false;

        if (!res) {
          return;
        }

        this.router.navigate(['/']);
      });
  } 

  showPassword = false;

  togglePasswordVisibility(): void {
    this.showPassword = !this.showPassword;
  }
}
