import { Injectable } from '@angular/core';
import { MessageService } from 'primeng/api';

/**
 * Wrapper sobre PrimeNG MessageService.
 * Mantiene la misma firma pública que la implementación anterior
 * para que todos los consumidores existentes funcionen sin cambios.
 *
 * Requiere:
 *  - MessageService provisto en app.config.ts (providedIn: 'root' no alcanza para PrimeNG)
 *  - <p-toast> en app.component.html (una sola instancia global)
 */
@Injectable({
  providedIn: 'root',
})
export class AlertService {
  constructor(private messageService: MessageService) {}

  showAlert(message: string, type: string): void {
    this.messageService.add({
      severity: this.mapSeverity(type),
      summary:  this.mapSummary(type),
      detail:   message,
      life:     4000,
    });
  }

  /** Limpia todos los toasts visibles */
  clearAll(): void {
    this.messageService.clear();
  }

  private mapSeverity(type: string): 'success' | 'info' | 'warn' | 'error' {
    switch (type) {
      case 'success': return 'success';
      case 'info':    return 'info';
      case 'warning': return 'warn';
      case 'error':   return 'error';
      default:        return 'info';
    }
  }

  private mapSummary(type: string): string {
    switch (type) {
      case 'success': return 'Éxito';
      case 'info':    return 'Información';
      case 'warning': return 'Atención';
      case 'error':   return 'Error';
      default:        return '';
    }
  }
}

export default AlertService;