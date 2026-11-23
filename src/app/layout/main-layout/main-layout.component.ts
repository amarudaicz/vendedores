import { Component } from '@angular/core';
import {RouterOutlet} from '@angular/router'
import { MainNavComponent } from "../main-nav/main-nav.component";
import { HeaderComponent } from "../header/header.component";
import { ConfigComponent } from "../../config/config.component";

@Component({
    selector: 'app-main-layout',
    imports: [RouterOutlet, MainNavComponent, HeaderComponent, ConfigComponent],
    templateUrl: './main-layout.component.html',
    styleUrl: './main-layout.component.scss'
})
export class MainLayoutComponent {

}
