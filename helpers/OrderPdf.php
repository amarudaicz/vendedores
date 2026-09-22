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
        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += (float)$item->getPrice() * (float)$item->getQuantity();
        }

        $discount = 0.0;

        if ($customer) {
            $discount = (float)$customer->getDescuento();
        }

        $discountAmount = $subtotal * ($discount / 100);
        $finalTotal = $subtotal - $discountAmount;

        $buyerName = '';
        $buyerId = '';
        if ($customer) {
            $buyerName = method_exists($customer, 'getName') ? $customer->getName() : ($customer['name'] ?? '');
            $buyerId = method_exists($customer, 'getDni') ? $customer->getDni() : ($customer['dni'] ?? '');
        } elseif ($guest) {
            $buyerName = method_exists($guest, 'getName') ? $guest->getName() : ($guest['name'] ?? '');
            $buyerId = method_exists($guest, 'getTin') ? $guest->getTin() : ($guest['tin'] ?? '');
        }

        $rows = '';
        foreach ($items as $item) {
            $itemSubtotal = (float)$item->getPrice() * (float)$item->getQuantity();
            $rows .= '<tr>'
                . '<td style="border:1px solid #ccc;padding:6px;">' . htmlspecialchars((string)$item->getProductCode()) . '</td>'
                . '<td style="border:1px solid #ccc;padding:6px;">' . htmlspecialchars((string)$item->getDescription()) . '</td>'
                . '<td style="border:1px solid #ccc;padding:6px;text-align:center;">' . $item->getQuantity() . '</td>'
                . '<td style="border:1px solid #ccc;padding:6px;text-align:right;">' . number_format($item->getPrice(), 2) . '</td>'
                . '<td style="border:1px solid #ccc;padding:6px;text-align:right;font-weight:bold;">' . number_format($itemSubtotal, 2) . '</td>'
                . '</tr>';
        }

        $totalsHtml = '<table width="100%" style="margin-top:8px;font-size:10pt;">';
        if ($discount > 0) {
            $totalsHtml .= '
            <tr>
                <td align="right">
                    <div><strong>SUBTOTAL $:</strong> ' . number_format($subtotal, 2) . '</div>
                    <div style="color:#c00;"><strong>DESCUENTO (' . number_format($discount, 2) . '%):</strong> -$' . number_format($discountAmount, 2) . '</div>
                    <div style="font-size:11pt;margin-top:4px;"><strong>TOTAL $:</strong> ' . number_format($finalTotal, 2) . '</div>
                </td>
            </tr>';
        } else {
            $totalsHtml .= '
            <tr>
                <td align="right"><strong>TOTAL $:</strong> ' . number_format($subtotal, 2) . '</td>
            </tr>';
        }
        $totalsHtml .= '</table>';

        $paymentMethod = method_exists($order, 'getPaymentMethod') ? $order->getPaymentMethod() : ($order['payment_method'] ?? '');
        $deliveryMethod = method_exists($order, 'getDeliveryMethod') ? $order->getDeliveryMethod() : ($order['delivery_method'] ?? '');

        $transporteNombre = $order->getTransporte()['nombre'] ?? null;

        $footerHtml = '<div style="margin-top:12px;font-size:9pt;">';
        if (!empty($paymentMethod)) {
            $footerHtml .= '<div><strong>Método de pago:</strong> ' . htmlspecialchars($paymentMethod) . '</div>';
        }
        if (!empty($deliveryMethod)) {
            $footerHtml .= '<div><strong>Método de envío:</strong> ' . htmlspecialchars($deliveryMethod) . '</div>';
        }
        if (!empty($transporteNombre)) {
            $footerHtml .= '<div><strong>Transporte:</strong> ' . htmlspecialchars($transporteNombre) . '</div>';
        }
        $footerHtml .= '</div>';

        $orderId = method_exists($order, 'getId') ? $order->getId() : ($order['id'] ?? 0);
        $orderCreatedAt = method_exists($order, 'getCreatedAt') ? $order->getCreatedAt() : ($order['created_at'] ?? 'now');

        return '
        <div style="font-size:10pt;color:#666;text-align:center;">Documento no válido como factura</div>
        <table width="100%" style="margin-top:8px;font-size:10pt;">
            <tr>
                <td>
                    ' . ($buyerName ? '<strong>Cliente:</strong> ' . htmlspecialchars($buyerName) . '<br>' : '') . '
                    ' . ($buyerId ? '<strong>Nro. Doc:</strong> ' . htmlspecialchars($buyerId) : '') . '
                </td>
                <td align="right">
                    <strong>Nota de Pedido N&deg;:</strong> ' . sprintf('%08d', $orderId) . '<br>
                    <strong>Fecha:</strong> ' . date('d/m/Y H:i', strtotime($orderCreatedAt)) . '
                </td>
            </tr>
        </table>
        <table width="100%" style="margin-top:12px;border-collapse:collapse;font-size:9pt;">
            <thead>
                <tr>
                    <th style="border:1px solid #ccc;padding:6px;text-align:left;">Cód.</th>
                    <th style="border:1px solid #ccc;padding:6px;text-align:left;">Descripcion</th>
                    <th style="border:1px solid #ccc;padding:6px;">Cant.</th>
                    <th style="border:1px solid #ccc;padding:6px;">Pr.U. $</th>
                    <th style="border:1px solid #ccc;padding:6px;">Tot. $</th>
                </tr>
            </thead>
            <tbody>' . $rows . '</tbody>
        </table>
        ' . $totalsHtml . '
        ' . $footerHtml;
    }
}