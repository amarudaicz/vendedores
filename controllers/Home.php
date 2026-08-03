<?php

namespace controllers;

use Exception;
use helpers\Logger;
use helpers\Response;
use TCPDF;
use models\Product;

/**
 *
 */
abstract class Home
{
    /**
     * @return void
     * @throws Exception
     */
    public static function home(): void
    {
        echo file_get_contents('public/pages/sellers/app/dist/browser/index.html');
    }

    /**
     * @return void
     */
    public static function products(): void
    {
        echo file_get_contents('public/pages/products/products.html');
    }


    /**
     * @return void
     */
    public static function fragancias(): void
    {
        echo file_get_contents('public/pages/fragancias/fragancias.html');
    }

    /**
     * @return void
     */
    public static function promotions(): void
    {
        echo file_get_contents('public/pages/promotions/promotions.html');
    }

    /**
     * @return void
     * @throws Exception
     */
    public static function shoppingCart(): void
    {
        echo file_get_contents('public/pages/shopping-cart/shopping-cart.html');
    }
    public static function downloadPDF(): void
    {
        try {
            set_time_limit(0);
            ini_set('memory_limit', '256M');

            $category = $_GET['cat'] ?? null;
            $subcategory = $_GET['subcat'] ?? null;

            // Initialize mPDF with appropriate settings
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 15,  // Extra space for header
                'margin_bottom' => 15,
                'margin_header' => 2,
                'margin_footer' => 10,
                'default_font' => 'helvetica',
                'autoPageBreak' => true,
                'autoPageBreakMargin' => 10,
            ]);

            $fechaActual = date('d/m/Y H:i', time() - 3600 * 3);

            // Configurar subtítulo del encabezado
            $headerSubtitle = $subcategory !== '0' ? $subcategory : ($category !== '0' ? $category : '');

            // Contenido del encabezado con fecha alineada a la derecha
            $headerContent = '
        <div style="color: #555; background-color: #fff; padding-top: 10px; font-size: 12pt;">
            <table width="100%">
                <tr>
                    <td style="text-align: left">
                    <span style="text-transform:uppercase; color:#36425b;">essencedubai.com.ar </span>' .
                ($headerSubtitle ? ' <div style="color: #495057;font-size:8pt; padding-left:5pt;">' . htmlspecialchars($headerSubtitle) . '</div>' : '') . '
                    </td>

                    <td style="text-align: right; font-size:9pt;">' . $fechaActual . '</td>
                </tr>
            </table>
        </div>';

            $mpdf->SetHTMLHeader($headerContent);
            $mpdf->SetHTMLFooter('<div style="text-align: right; font-size: 10pt;">Página {PAGENO} de {nbpg}</div>');


            // Get products and apply filters
            $productos = Product::getAvailableProducts();

            Logger::log('INFO', 'Descargando PDF en ' . date('Y-m-d H:i:s', time() - 10800) . " con la categoría $category y subcategoría $subcategory");

            if ($category && $category !== '0') {
                $productos = array_filter($productos, fn($p) => $p->getCategory()->getName() === $category);
            }

            if ($subcategory && $subcategory !== '0') {
                $productos = array_filter($productos, fn($p) => $p->getSubcategory()?->getName() === $subcategory);
            }


            $columnas = 3;
            $totalPorPagina = 9;
            $contador = 0;
            $tempDir = 'public/multimedia/temp';

            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0777, true);
            }

            $html = '<table style="border-collapse: collapse;"><tr>';

            foreach ($productos as $producto) {
                if ($contador > 0 && $contador % $columnas === 0) {
                    $html .= '</tr><tr>';
                }

                $imagePath = $producto->getImage()?->getFilePath();
                if ($imagePath && is_file($imagePath) && is_readable($imagePath)) {
                    $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION) ?: 'jpg');
                    $safeName = str_replace('.', '_', pathinfo($imagePath, PATHINFO_FILENAME));
                    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $safeName);
                    $safeName = md5($imagePath) . '-' . substr($safeName, 0, 30) . '.' . $ext;
                    $tempPath = $tempDir . '/' . $safeName;
                    if (!file_exists($tempPath)) {
                        @copy($imagePath, $tempPath);
                    }
                    $imageSrc = $tempPath;
                } else {
                    $imageSrc = 'public/multimedia/images/logo-claro.jpeg';
                }

                $html .= '<td style="border: 1px solid #CECECE;text-align:center; vertical-align:top; padding-bottom:15;">';
                $html .= '<div class="product">';
                $html .= '<img class="product-image" style="margin-bottom:20pt; width:100%; object-fit:cover;" height="800" src="' . $imageSrc . '">';
                $html .= '<div class="product-description" style="display:block; font-size:30pt; color:#36425b; font-weight:bold;">' . htmlspecialchars($producto->getName() ?? 'Sin descripción') . '</div>';
                $html .= '<br>';
                $html .= '<div class="product-code" style="font-size:30pt; color:#495057;">COD - ' . htmlspecialchars($producto->getCode() ?? 'Código no disponible') . '</div>';
                $html .= '</div></td>';

                $contador++;

                if ($contador % $totalPorPagina === 0 || $contador === count($productos)) {
                    $html .= '</tr></table>';
                    $mpdf->WriteHTML($html);

                    if ($contador < count($productos)) {
                        $html = '<table style="border-collapse: collapse;"><tr>';
                    }
                }
            }

            // Output
            $pdfContent = $mpdf->Output('', 'S');

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="productos.pdf"');
            header('Content-Length: ' . strlen($pdfContent));
            echo $pdfContent;
            exit;
        } catch (\Throwable $th) {
            http_response_code(500);
            echo 'Error al generar el PDF: ' . $th->getMessage();
        }
    }
    /**
     * @return void
     */
    public static function termsAndPolicy(): void
    {
        echo file_get_contents('public/pages/legals/legals.html');
    }
}
