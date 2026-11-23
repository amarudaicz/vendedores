import { NgIf } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
    selector: 'app-modal',
    imports: [NgIf],
    templateUrl: './modal.component.html',
    styleUrl: './modal.component.scss'
})
export class ModalComponent {
  @Input() isOpen: boolean = false; // Controla si el popup está abierto
  @Output() isOpenChange = new EventEmitter<boolean>(); // Para comunicar el estado al padre

  close() {
    this.isOpen = false;
    this.isOpenChange.emit(this.isOpen);
  }
}
