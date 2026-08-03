<?php

namespace helpers;

use models\Customer;
use models\Guest;
use models\Order;
use models\OrderItem;

/**
 * Genera el PDF (Nota de Pedido) de una orden en el servidor utilizando mPDF.
 */
abstract class OrderPdf
{
    /**
     * @param Order        $order
     * @param OrderItem[]  $items
     * @param Customer|null $customer
     * @param Guest|null   $guest
     *
     * @return string Contenido binario del PDF.
     */
    public static function generate(Order $order, array $items, ?Customer $customer, ?Guest $guest): string
    {
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'default_font' => 'helvetica',
            'autoPageBreak' => true,
        ]);

        $mpdf->WriteHTML(self::buildHtml($order, $items, $customer, $guest), \Mpdf\HTMLParserMode::HTML_BODY);

        return $mpdf->Output('', 'S');
    }

    /**
     * @param Order        $order
     * @param OrderItem[]  $items
     * @param Customer|null $customer
     * @param Guest|null   $guest
     *
     * @return string
     */
    private static function buildHtml(Order $order, array $items, ?Customer $customer, ?Guest $guest): string
    {
        $total = 0.0;
        foreach ($items as $item) {
            $total += (float)$item->getPrice() * (float)$item->getQuantity();
        }

        $buyerName = '';
        $buyerId = '';
        if ($customer) {
            $buyerName = $customer->getName();
            $buyerId = $customer->getDni();
        } elseif ($guest) {
            $buyerName = $guest->getName();
            $buyerId = $guest->getTin();
        }

        $rows = '';
        foreach ($items as $item) {
            $subtotal = (float)$item->getPrice() * (float)$item->getQuantity();
            $rows .= '<tr>'
                . '<td style="border:1px solid #ccc;padding:6px;">' . htmlspecialchars((string)$item->getDescription()) . '</td>'
                . '<td style="border:1px solid #ccc;padding:6px;text-align:center;">' . $item->getQuantity() . '</td>'
                . '<td style="border:1px solid #ccc;padding:6px;text-align:right;">' . number_format($item->getPrice(), 2) . '</td>'
                . '<td style="border:1px solid #ccc;padding:6px;text-align:right;font-weight:bold;">' . number_format($subtotal, 2) . '</td>'
                . '</tr>';
        }

        return '
        <div style="font-size:10pt;color:#666;text-align:center;">Documento no válido como factura</div>
        <table width="100%" style="margin-top:8px;font-size:10pt;">
            <tr>
                <td>
                    ' . ($buyerName ? '<strong>Cliente:</strong> ' . htmlspecialchars($buyerName) . '<br>' : '') . '
                    ' . ($buyerId ? '<strong>Nro. Doc:</strong> ' . htmlspecialchars($buyerId) : '') . '
                </td>
                <td align="right">
                    <strong>Nota de Pedido N&deg;:</strong> ' . sprintf('%08d', $order->getId()) . '<br>
                    <strong>Fecha:</strong> ' . date('d/m/Y H:i', strtotime($order->getCreatedAt())) . '
                </td>
            </tr>
        </table>
        <table width="100%" style="margin-top:12px;border-collapse:collapse;font-size:9pt;">
            <thead>
                <tr>
                    <th style="border:1px solid #ccc;padding:6px;text-align:left;">Item</th>
                    <th style="border:1px solid #ccc;padding:6px;">Cant.</th>
                    <th style="border:1px solid #ccc;padding:6px;">Pr.U. $</th>
                    <th style="border:1px solid #ccc;padding:6px;">Tot. $</th>
                </tr>
            </thead>
            <tbody>' . $rows . '</tbody>
        </table>
        <table width="100%" style="margin-top:8px;font-size:10pt;">
            <tr>
                <td align="right"><strong>TOTAL $:</strong> ' . number_format($total, 2) . '</td>
            </tr>
        </table>
        <div style="margin-top:12px;font-size:9pt;">
            ' . ($order->getNote() !== '' ? '<div><strong>Nota:</strong> ' . htmlspecialchars($order->getNote()) . '</div>' : '') . '
            ' . ($order->getPaymentMethod() !== '' ? '<div><strong>M&eacute;todo de pago:</strong> ' . htmlspecialchars($order->getPaymentMethod()) . '</div>' : '') . '
        </div>';
    }
}