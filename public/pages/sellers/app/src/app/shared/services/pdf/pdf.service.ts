import { Injectable, Renderer2, RendererFactory2 } from '@angular/core';
// @ts-ignore
import html2pdf from 'html2pdf.js';

@Injectable({
  providedIn: 'root'
})
export class PdfService {
  private renderer: Renderer2;

  constructor(rendererFactory: RendererFactory2) {
    this.renderer = rendererFactory.createRenderer(null, null);
  }

  printPDF(elementId: string, fileName: string): void {
    const content = document.getElementById(elementId);
    if (!content) {
      console.error(`Elemento con id ${elementId} no encontrado.`);
      return;
    }

    const options = {
      margin:       10,
      filename:     `${fileName}.pdf`,
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2 },
      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().from(content).set(options).save();
  }
}
