import { Component, OnInit } from '@angular/core';
import { AuthService } from '../auth/services/auth.service';
import { Router } from '@angular/router';
import { catchError, of } from 'rxjs';

@Component({
  selector: 'app-config',
  imports: [],
  templateUrl: './config.component.html',
  styleUrl: './config.component.scss',
})
export class ConfigComponent implements OnInit {
  constructor(private auth: AuthService, private router: Router) {}

  ngOnInit() {
    this.auth
      .isLogged()
      .pipe(
        catchError((err) => {
          // this.router.navigate(['/login']);
          return of({ data: { seller: null } });
        })
      )
      .subscribe((logged: any) => {
        console.log(logged);
        // this.auth.nextSeller(logged.data.seller);
      });
  }
}
