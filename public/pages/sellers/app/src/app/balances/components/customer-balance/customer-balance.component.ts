import { Component } from '@angular/core';
import { BalanceService } from '../../services/balance.service';
import { NgxSkeletonLoaderModule } from 'ngx-skeleton-loader';
import { CommonModule, CurrencyPipe } from '@angular/common';
import { ProductsService } from '../../../shared/services/products/products.service';
import { CustomersService } from '../../../clients/services/customers.service';
import { environment } from '../../../../../environment';

@Component({
  selector: 'app-customer-balance',
  imports: [NgxSkeletonLoaderModule, CurrencyPipe, CommonModule],
  templateUrl: './customer-balance.component.html',
  styleUrl: './customer-balance.component.scss',
})
export class CustomerBalanceComponent {
  receipts: any[] | undefined = undefined;
  filtredReceipts: any[] | undefined = undefined;
  dolar: number | null = null;
  isGeneratingPdf = false;

  constructor(
    public balanceService: BalanceService,
    private productService: ProductsService,
    private customersService: CustomersService
  ) {
    this.balanceService.getPayments().subscribe((res) => {
      this.receipts = res.data.receipts;
      this.filtredReceipts = res.data.receipts;
      this.balanceService.totalBalance.set(res.data.total_balance);
      this.balanceService.lastReceiptDate.set(res.data.receipts[0]?.date_receipt);
      this.balanceService.receipts.set(res.data.receipts);
    });

    this.productService.dolar$.subscribe((dolar) => {
      this.dolar = dolar;
    });
  }

  getTotal(field: 'subtotal_receipt' | 'iva_receipt' | 'total_receipt' | 'balance_receipt'): number {
    return (
      this.balanceService.receipts()?.reduce((acc, curr) => {
        return acc + parseFloat(curr[field] || '0');
      }, 0) || 0
    );
  }

  private formatCurrency(value: string | number): string {
    const num = parseFloat(String(value) || '0');
    return new Intl.NumberFormat('es-AR', {
      style: 'currency',
      currency: 'ARS',
      minimumFractionDigits: 2,
    }).format(num);
  }

  async downloadPdf(): Promise<void> {
    if (this.isGeneratingPdf) return;
    this.isGeneratingPdf = true;

    try {
      const html2pdf = (await import('html2pdf.js')).default;
      const customer = this.customersService.customer();
      const receipts = this.balanceService.receipts() || [];
      const today = new Date().toLocaleDateString('es-AR', {
        day: '2-digit', month: '2-digit', year: 'numeric',
      });

      const subtotal = this.getTotal('subtotal_receipt');
      const iva = this.getTotal('iva_receipt');
      const total = this.getTotal('total_receipt');
      const balance = this.getTotal('balance_receipt');

      // --- Build inline HTML string ---
      // Pass as STRING to .from() so html2pdf renders in its own managed container.
      // This avoids the visibility:hidden / left:-9999px height=0 bug in html2canvas.
      const TD = 'padding:6px 8px;font-size:11px;border-bottom:1px solid #e5e7eb;vertical-align:middle;';
      const TH = 'padding:8px 8px;font-size:11px;font-weight:600;color:#fff;white-space:nowrap;';
      const TF = 'padding:8px 8px;font-size:11px;font-weight:700;color:#fff;';

      const rowsHtml = receipts.map((c: any, i: number) => {
        const bg = i % 2 === 0 ? '#fff' : '#f9fafb';
        const td = `${TD}background:${bg};`;
        return `<tr>
          <td style="${td}">${c.date_receipt ?? ''}</td>
          <td style="${td}">${c.type_receipt ?? ''}</td>
          <td style="${td}">${c.number_receipt ?? ''}</td>
          <td style="${td}text-align:right;">${this.formatCurrency(c.subtotal_receipt)}</td>
          <td style="${td}text-align:right;">${this.formatCurrency(c.iva_receipt)}</td>
          <td style="${td}text-align:right;font-weight:700;">${this.formatCurrency(c.total_receipt)}</td>
          <td style="${td}text-align:right;font-weight:700;">${this.formatCurrency(c.balance_receipt)}</td>
          <td style="${td}text-align:right;">${this.formatCurrency(c.balance_accumulated)}</td>
          <td style="${td}">${c.condition_sale ?? ''}</td>
          <td style="${td}text-align:center;">${c.delay_receipt ?? ''}</td>
        </tr>`;
      }).join('');

      const htmlString = `
        <div style="font-family:Helvetica Neue,Helvetica,Arial,sans-serif;
                    font-size:11px;color:#1a1a2e;background:#fff;padding:24px;width:680px;">

          <div style="display:flex;justify-content:space-between;align-items:flex-start;
                      margin-bottom:20px;padding-bottom:14px;border-bottom:2px solid #1a1a2e;">
            <div style="font-size:22px;font-weight:800;letter-spacing:-0.5px;color:#1a1a2e;">
              ${environment.empresa.nombre} <span style="color:#4f46e5;">.</span>
            </div>
            <div style="text-align:right;font-size:8px;color:#6b7280;line-height:1.8;">
              <div style="color:#1a1a2e;font-size:12px;font-weight:700;margin-bottom:2px;">Cuenta Corriente</div>
              ${customer?.name ?? ''} &nbsp;|&nbsp; C&oacute;digo: ${customer?.code ?? ''}<br>
              Generado: ${today}
            </div>
          </div>

          <div style="font-size:13px;font-weight:700;margin-bottom:4px;color:#1a1a2e;">Estado de Cuenta</div>
          <div style="font-size:9px;color:#6b7280;margin-bottom:16px;">Listado completo de comprobantes del cliente</div>

          <table style="width:100%;border-collapse:collapse;">
            <thead>
              <tr style="background:#1a1a2e;">
                <th style="${TH}text-align:left;">Fecha</th>
                <th style="${TH}text-align:left;">Tipo</th>
                <th style="${TH}text-align:left;">Cbte</th>
                <th style="${TH}text-align:right;">Subtotal</th>
                <th style="${TH}text-align:right;">IVA</th>
                <th style="${TH}text-align:right;">Total</th>
                <th style="${TH}text-align:right;">Saldo</th>
                <th style="${TH}text-align:right;">Acumulado</th>
                <th style="${TH}text-align:left;">Cond. Venta</th>
                <th style="${TH}text-align:center;">Demora (d)</th>
              </tr>
            </thead>
            <tbody>${rowsHtml}</tbody>
            <tfoot>
              <tr style="background:#1a1a2e;">
                <td colspan="3" style="${TF}"></td>
                <td style="${TF}text-align:right;">${this.formatCurrency(subtotal)}</td>
                <td style="${TF}text-align:right;">${this.formatCurrency(iva)}</td>
                <td style="${TF}text-align:right;">${this.formatCurrency(total)}</td>
                <td style="${TF}text-align:right;">${this.formatCurrency(balance)}</td>
                <td colspan="3" style="${TF}"></td>
              </tr>
            </tfoot>
          </table>

          <div style="margin-top:16px;display:flex;justify-content:space-between;
                      font-size:7.5px;color:#9ca3af;border-top:1px solid #e5e7eb;padding-top:8px;">
            <span>${customer?.name ?? ''} &nbsp;&middot;&nbsp; ${today}</span>
          </div>
        </div>
      `;

      const fileName = `cuenta-corriente-${customer?.code ?? 'cliente'}-${today.replace(/\//g, '-')}.pdf`;

      await html2pdf()
        .set({
          margin: [8, 8, 8, 8],
          filename: fileName,
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: {
            scale: 2,
            useCORS: true,
            logging: false,
            allowTaint: true,
            windowWidth: 728,
          },
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
          pagebreak: { mode: ['css', 'legacy'] },
        })
        .from(htmlString)
        .save();

    } finally {
      this.isGeneratingPdf = false;
    }
  }
}
