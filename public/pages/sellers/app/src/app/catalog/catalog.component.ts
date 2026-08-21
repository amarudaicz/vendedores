import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { SelectModule } from 'primeng/select';
import { ButtonModule } from 'primeng/button';
import { environment } from '../../../environment';

interface PriceListOption {
  label: string;
  value: number;
}

@Component({
  selector: 'app-catalog',
  standalone: true,
  imports: [CommonModule, FormsModule, SelectModule, ButtonModule],
  templateUrl: './catalog.component.html',
})
export class CatalogComponent {
  priceLists: PriceListOption[] = [
    { label: 'Lista 1', value: 1 },
    { label: 'Lista 2', value: 2 },
    { label: 'Lista 3', value: 3 },
    { label: 'Lista 4', value: 4 },
    { label: 'Lista 5', value: 5 },
    { label: 'Lista 6', value: 6 },
  ];

  selectedList: PriceListOption = this.priceLists[0];
  isLoading = signal(false);

  constructor(private http: HttpClient) {}

  downloadCatalog(): void {
    if (this.isLoading()) return;

    this.isLoading.set(true);

    const url = `${environment.apiUrl}products/catalog/excel?list=${this.selectedList.value}`;

    this.http
      .get(url, {
        responseType: 'blob',
        withCredentials: true,
      })
      .subscribe({
        next: (blob) => {
          const now = new Date();
          const pad = (n: number) => String(n).padStart(2, '0');
          const datetime =
            `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;+
          console.log(datetime);
          
          const objectUrl = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = objectUrl;
          a.download = `catalogo_lista_${this.selectedList.value}_${datetime}.xlsx`;
          a.click();
          URL.revokeObjectURL(objectUrl);
          this.isLoading.set(false);
        },
        error: () => {
          this.isLoading.set(false);
        },
      });
  }
}
