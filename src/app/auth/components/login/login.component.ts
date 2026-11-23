import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  ReactiveFormsModule,
} from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { catchError, of } from 'rxjs';
import { version } from '../../../version';

@Component({
  selector: 'app-login',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.scss',
})
export class LoginComponent {
  form: FormGroup;
  error: string | null = null;
  loading = false;
  version = version;
  showPassword = false;

  constructor(
    private auth: AuthService,
    private fb: FormBuilder,
    private router: Router,
    private route: ActivatedRoute,
  ) {
    this.form = this.fb.group({
      email: [null],
      password: [null],
    });
  }

  login(): void {
    this.error = null;
    this.loading = true;
    this.auth
      .logIn(this.form.value)
      .pipe(
        catchError((err) => {
          this.error =
            err?.error?.message ||
            err?.error?.error?.message ||
            'Credenciales inválidas';
          this.loading = false;
          return of(false);
        }),
      )
      .subscribe((res) => {
        this.loading = false;

        if (!res) {
          return;
        }

        const returnUrl =
          this.route.snapshot.queryParamMap.get('returnUrl') || '/';
        this.router.navigateByUrl(returnUrl);
      });
  }

  togglePasswordVisibility(): void {
    this.showPassword = !this.showPassword;
  }
}
