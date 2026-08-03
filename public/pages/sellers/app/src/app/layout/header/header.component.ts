import { Component, ElementRef, HostListener } from '@angular/core';
import { AuthService } from '../../auth/services/auth.service';
import { catchError, of } from 'rxjs';
import AlertService from '../../shared/services/alert/alert.service';
import { Router } from '@angular/router';
import { MainNavComponent } from "../main-nav/main-nav.component";
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-header',
  imports: [MainNavComponent, CommonModule],
  templateUrl: './header.component.html',
  styleUrl: './header.component.scss'
})
export class HeaderComponent {

  constructor(public auth: AuthService, private alerts: AlertService, private router: Router, private elementRef: ElementRef) { }

  isOpen = false;

  toggleMenu() {
    this.isOpen = !this.isOpen;
  }

  logOut() {
    this.auth.logOut().pipe(
      catchError((error) => {
        this.alerts.showAlert('error', 'No se pudo cerrar la sesión')
        return of(error)
      })
    ).subscribe(() => {
      this.router.navigate(['/login'])
    })
  }


  @HostListener('document:click', ['$event'])
  onClickOutside(event: MouseEvent) {
    const clickedInside = this.elementRef.nativeElement.contains(event.target);
    if (!clickedInside && this.isOpen) {
      this.isOpen = false;
    }
  }
}
