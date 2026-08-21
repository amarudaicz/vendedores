<?php

namespace api;

use filters\AccountFilter;
use filters\SessionFilter;
use helpers\Request;
use helpers\Response;
use JsonException;
use models\Customer;
use models\Image;
use models\Product;
use api\exceptions\ApiException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 *
 */
abstract class Products {
    /**
     * @param array $products
     * @return void
     */
    private static function updateProductPrice(array $products): void {
        $account = !empty($_SESSION['account']) ? unserialize($_SESSION['account']) : null;

        if (!empty($account) && $account instanceof Customer) {
            /** @var Product $product */
            array_map(function ($product) use ($account) {
                $product->setPrice($product->getCustomerPrice($account->getPriceList()));
            }, $products);
        }
    }

    /**
     * @return void
     * @throws ApiException
     */
    public static function getProducts(): void {
        $products = [];

        if (isset($_GET['featured']) && $_GET['featured'] === '1') {
            $products = Product::getFeaturedProducts();
            self::updateProductPrice($products);
        }

        if (isset($_GET['featured']) && $_GET['featured'] === '0') {
            $products = Product::getAvailableProducts();
            self::updateProductPrice($products);
        }

        if (!isset($_GET['featured'])) {
            SessionFilter::validateApiSession();
            AccountFilter::filterApiCustomerAccount();
            $products = Product::getProducts();
        }

        Response::append('products', $products);

        Response::setCode(200);
    }

    /**
     * @param string $productId
     *
     * @return void
     * @throws ApiException
     */
    public static function getProduct(string $productId): void {
        $product = Product::getProductByCode($productId);

        if (empty($product))
            throw new ApiException('El producto no existe', 404);

        Response::append('product', $product);

        Response::setCode(200);
    }

    /**
     * @param string $productCode
     *
     * @return void
     * @throws ApiException
     * @throws JsonException
     */
    public static function updateProduct(string $productCode): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $data = Request::getJson();

        $product = Product::getProductByCode($productCode);

        if (empty($product))
            throw new ApiException('El producto no existe', 404);

        if (isset($data->description))
            $product->setDescription($data->description);

        if (isset($data->featured))
            $product->setFeatured($data->featured);

        if (isset($data->imageId)) {
            $image = Image::getImageAssignedToProductCode($product->getCode());

            if (!empty($image)) {
                Product::unassignImage($product->getCode(), $image->getId());
            }

            $image = Image::getImageById($data->imageId);

            if (empty($image))
                throw new ApiException('La imagen no existe', 404);

            Product::assignImage($productCode, $data->imageId);
        }

        Product::updateProduct($product);

        $product = Product::getProductByCode($product->getCode());

        Response::append('product', $product);

        Response::setCode(200);
    }

    public static function getDolar()
    {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $dolar = Product::getDolar();

        Response::append('dolar', $dolar);
    }

    /**
     * Genera y descarga un catálogo de productos en formato Excel (.xlsx)
     * filtrado por lista de precios.
     *
     * @return void
     * @throws ApiException
     */
    public static function getCatalogExcel(): void
    {
        SessionFilter::validateApiSession();

        $list = isset($_GET['list']) ? (int) $_GET['list'] : null;

        if ($list === null || $list < 1 || $list > 6) {
            throw new ApiException('El parámetro list debe ser un número entre 1 y 6', 422);
        }

        $products = Product::getProducts();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Catálogo Lista ' . $list);

        // --- Cabeceras ---
        $headers = ['Código', 'Nombre', 'Descripción', 'Stock', 'Precio', 'Categoría', 'Subcategoría'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Estilo de cabecera
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // --- Datos ---
        $row = 2;
        /** @var Product $product */
        foreach ($products as $product) {
            $price = $product->getCustomerPrice($list);

            $sheet->setCellValue('A' . $row, $product->getCode());
            $sheet->setCellValue('B' . $row, $product->getName());
            $sheet->setCellValue('C' . $row, $product->getDescription());
            $sheet->setCellValue('D' . $row, $product->getStock());
            $sheet->setCellValue('E' . $row, $price);
            $sheet->setCellValue('F' . $row, $product->getCategory() ? $product->getCategory()->getName() : '');
            $sheet->setCellValue('G' . $row, $product->getSubcategory() ? $product->getSubcategory()->getName() : '');

            // Formato numérico para precio y stock
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            // Alternar color de fila
            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF0F4F8']],
                ]);
            }

            $row++;
        }

        // Auto-ajuste de columnas
        foreach (range('A', 'G') as $colLetter) {
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // --- Enviar el archivo al browser ---
        $filename = 'catalogo_lista_' . $list . '.xlsx';

        // Limpiar cualquier output previo
        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }
}