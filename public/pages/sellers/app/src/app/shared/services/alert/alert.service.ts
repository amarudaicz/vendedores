import { Injectable } from "@angular/core";

@Injectable({
  providedIn: 'root'
})

export class AlertService {
  private alerts: { id: number; message: string; type: string; isVisible: boolean }[] = [];
  private nextId: number = 1;

  showAlert(message: string, type: string): void {
      const alert = {
          id: this.nextId++,
          message,
          type,
          isVisible: true,
      };
      this.alerts.push(alert);
      this.open(alert);
  }

  dismissAlert(id: number): void {
      const alert = this.alerts.find(alert => alert.id === id);
      if (alert) {
          alert.isVisible = false
          this.updateAlertDisplay(alert);
      }
  }

  private open(alert: { id: number; message: string; type: string; isVisible: boolean }): void {
      let alertContainer = document.getElementById('alert-container');

      if (!alertContainer) {
          alertContainer = document.createElement('div');
          alertContainer.id = 'alert-container';
          alertContainer.className = 'fixed top-4 right-4 flex flex-col gap-3 z-[9999] pointer-events-none'; 
          document.body.appendChild(alertContainer);
      }

      const alertElement = document.createElement('div');
      alertElement.id = `alert-${alert.id}`;
      // Basic card styles
      alertElement.className = `
          pointer-events-auto 
          flex w-full max-w-sm overflow-hidden bg-white rounded-lg shadow-lg 
          transform transition-all duration-300 translate-x-10 opacity-0
      `;

      // Define colors and icons based on type
      let iconSvg = '';
      let colorClass = '';
      let alertType = ''
      
      switch (alert.type) {
          case 'error':
              colorClass = 'bg-red-500';
              iconSvg = `<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
                alertType = 'Error';
              break;
          case 'info':
              colorClass = 'bg-blue-500';
              iconSvg = `<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
              alertType = 'Info';
              break;
          case 'success':
              colorClass = 'bg-green-500';
              iconSvg = `<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
              alertType = 'Exito';
              break;
          default:
              colorClass = 'bg-gray-500';
              iconSvg = '';
      }

      alertElement.innerHTML = `
          <div class="flex items-center justify-center w-12 ${colorClass}">
              ${iconSvg}
          </div>
          <div class="px-4 py-2 -mx-3">
              <div class="mx-3">
                  <span class="font-semibold ${alert.type === 'error' ? 'text-red-500' : (alert.type === 'info' ? 'text-blue-500' : 'text-green-500')}">
                      ${alertType}
                  </span>
                  <p class="text-sm text-gray-600 break-words">${alert.message}</p>
              </div>
          </div>
      `;

      alertContainer.appendChild(alertElement);

      // Trigger animation
      requestAnimationFrame(() => {
          alertElement.classList.remove('translate-x-10', 'opacity-0');
      });

      // Auto dismiss
      setTimeout(() => {
          this.dismissAlert(alert.id);
      }, 3000);
  }

  private updateAlertDisplay(alert: { id: number; message: string; type: string; isVisible: boolean }): void {
      const alertElement = document.getElementById(`alert-${alert.id}`);
      if (alertElement) {
          // Fade out animation
          alertElement.classList.add('opacity-0', 'translate-x-10');
          // Remove after animation
          setTimeout(() => {
              alertElement.remove();
          }, 300);
      }
  }
}

export default AlertService;