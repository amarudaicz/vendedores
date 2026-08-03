import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { HeaderComponent } from './layout/header/header.component';
import { ConfigComponent } from './config/config.component';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, HeaderComponent, ConfigComponent],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss',
})
export class AppComponent {
  title = 'app';
}
