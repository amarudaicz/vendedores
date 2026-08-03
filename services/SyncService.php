<?php

namespace services;

use api\exceptions\ApiException;
use helpers\Logger;
use models\Category;
use models\Connection;
use models\Customer;
use models\Payment;
use models\Product;
use models\Seller;
use models\Subcategory;

class SyncService
{
    /**
     * @var string
     */
    public static string $updatedAt = '';

    /**
     * @var string
     */
    public static string $basePath = '';

    /**
     * Sets the base path and timestamp to use for the sync.
     * 
     * @param string $basePath
     * @param string $updatedAt
     */
    public static function init(string $basePath, string $updatedAt): void
    {
        self::$basePath = rtrim($basePath, '/\\');
        self::$updatedAt = $updatedAt;
    }

    /**
     * Runs all the import procedures.
     * Ensure you have called init() before.
     *
     * @throws ApiException
     * @throws \Exception
     */
    public static function runAllImportProcedures(): void
    {
        if (empty(self::$updatedAt)) {
            throw new \RuntimeException('SyncService must be initialized before running imports.');
        }
        Logger::log('INFO', 'Importing categories from csv file');
        self::importCategoriesFromCsvFile();
        Logger::log('INFO', 'Importing subcategories from csv file');
        self::importSubcategoriesFromCsvFile();
        Logger::log('INFO', 'Importing products from csv file');
        self::importProductsFromCsvFile();
        Logger::log('INFO', 'Importing clients from csv file');
        self::importCustomersFromCsvFile();
        Logger::log('INFO', 'Importing sellers from csv file');
        self::importSellersFromCsvFile();
        Logger::log('INFO', 'Importing balances from csv file');
        self::importBalancesFromCsvFile();
    }

    /**
     * @return void
     * @throws ApiException
     */
    public static function importCategoriesFromCsvFile(): void
    {
        Logger::log('INFO', 'Importing categories from csv file');

        $categoryCSVFile = self::$basePath . "/writable/files/generos.csv";

        if (!file_exists($categoryCSVFile))
            throw new ApiException('File ' . $categoryCSVFile . ' does not exist');

        $fd = fopen($categoryCSVFile, "r");

        if ($fd === false)
            throw new ApiException("Error opening file " . $categoryCSVFile);

        while (($row = fgetcsv($fd, null, ";")) !== false) {
            $csvCategoryCode = $row[0];
            $csvCategoryName = iconv('latin1', 'utf8', $row[1]);

            $category = new Category();
            $category->setCode($csvCategoryCode);
            $category->setName($csvCategoryName);
            $category->setCreatedAt(date("Y-m-d H:i:s"));
            $category->setUpdatedAt(date("Y-m-d H:i:s"));
            Category::createUpdateCategory($category);
        }

        fclose($fd);
    }

    /**
     * @return void
     * @throws ApiException
     */
    public static function importSubcategoriesFromCsvFile(): void
    {
        Logger::log('INFO', 'Importing subcategories from csv file');

        $subcategoryCSVFile = self::$basePath . "/writable/files/familias.csv";

        if (!file_exists($subcategoryCSVFile))
            throw new ApiException('File ' . $subcategoryCSVFile . ' does not exist');

        $fd = fopen($subcategoryCSVFile, "r");

        if ($fd === false)
            throw new ApiException("Error opening file " . $subcategoryCSVFile);

        while (($row = fgetcsv($fd, null, ";")) !== false) {
            $csvSubcategoryCode = $row[0];
            $csvSubcategoryName = iconv('latin1', 'utf8', $row[1]);

            $category = new Subcategory();
            $category->setCode($csvSubcategoryCode);
            $category->setName($csvSubcategoryName);
            $category->setCreatedAt(date("Y-m-d H:i:s"));
            $category->setUpdatedAt(date("Y-m-d H:i:s"));
            Subcategory::createUpdateCategory($category);
        }

        fclose($fd);
    }

    /**
     * @return void
     * @throws ApiException
     */
    public static function importProductsFromCsvFile(): void
    {
        if (empty(self::$updatedAt)) {
            self::$updatedAt = date('Y-m-d H:i:s');
        }

        Logger::log('INFO', 'Importing products from csv file');

        $productCSVFile = self::$basePath . "/writable/files/articulo.csv";

        if (!file_exists($productCSVFile))
            throw new ApiException('File ' . $productCSVFile . ' does not exist');

        $fd = fopen($productCSVFile, "r");

        if ($fd === false)
            throw new ApiException("Error opening file " . $productCSVFile);

        while (($row = fgetcsv($fd, null, ";")) !== false) {
            $csvCategoryCode = trim($row[0]);
            $csvSubcategoryCode = trim($row[1]);
            $csvProductCode = trim($row[2]);
            $csvProductName = iconv('latin1', 'utf8', trim($row[3]));
            $csvProductPrice = str_replace(',', '.', trim($row[5]));
            $csvFeatured = trim($row[7]);
            $csvProductStock = str_replace(',', '.', trim($row[10]));
            $csvPrice1 = str_replace(',', '.', trim($row[12]));
            $csvPrice2 = str_replace(',', '.', trim($row[14]));
            $csvPrice3 = str_replace(',', '.', trim($row[16]));
            $csvPrice4 = str_replace(',', '.', trim($row[18]));
            $csvPrice5 = str_replace(',', '.', trim($row[20]));
            $csvPrice6 = str_replace(',', '.', trim($row[22]));
            $csvArsUsd = str_replace(',', '.', trim($row[24]));
            $csvProductSoloVendedores = trim($row[26]) === 'S';

            $product = new Product();
            $product->setCode($csvProductCode);
            $product->setName($csvProductName);
            $product->setStock($csvProductStock);
            $product->setPrice($csvProductPrice);
            $product->setPrice1($csvPrice1);
            $product->setPrice2($csvPrice2);
            $product->setPrice3($csvPrice3);
            $product->setPrice4($csvPrice4);
            $product->setPrice5($csvPrice5);
            $product->setPrice6($csvPrice6);
            $product->setArsUsd($csvArsUsd);
            $product->setFeatured($csvFeatured === 'S');
            $product->setDeleted(false);
            $product->setShowInSellers($csvProductSoloVendedores);
            $product->setUpdatedAt(self::$updatedAt);
            $product->setCategoryCode($csvCategoryCode);
            $product->setSubcategoryCode($csvSubcategoryCode);
            Product::createUpdateProduct($product);
        }

        fclose($fd);

        $unUpdatedProducts = Product::getNotUpdatedProducts(self::$updatedAt);

        /** @var Product $product */
        foreach ($unUpdatedProducts as $product) {
            Product::deleteProduct($product);
        }

    }

    /**
     * @return void
     * @throws ApiException
     * @throws Exception
     */
    public static function importCustomersFromCsvFile(): void
    {
        if (empty(self::$updatedAt)) {
            self::$updatedAt = date('Y-m-d H:i:s');
        }

        Logger::log('INFO', 'Importing customers from csv file');

        $customerCSVFile = self::$basePath . "/writable/files/clientes.csv";

        if (!file_exists($customerCSVFile))
            throw new ApiException('File ' . $customerCSVFile . ' does not exist');

        $fd = fopen($customerCSVFile, "r");

        if ($fd === false)
            throw new ApiException("Error opening file " . $customerCSVFile);

        while (($row = fgetcsv($fd, null, ";")) !== false) {
            Logger::log('INFO', 'Procesando cliente: ' . $row[3]);
            $csvCustomerZone = (int) $row[1];
            $csvCustomerCode = (int) $row[2];
            $csvCustomerName = iconv('latin1', 'utf8', $row[3]);
            // $csvCustomerName = $row[3];
            $csvCustomerDNI = $row[8];
            $csvCustomerPriceList = (int) $row[19];
            $csvCustomerSellerCode = $row[0];
            $customer = new Customer();
            $customer->setCode($csvCustomerCode);
            $customer->setName($csvCustomerName);
            $customer->setDni($csvCustomerDNI);
            $customer->setZone($csvCustomerZone);
            $customer->setPasswordSalt(base64_encode(random_bytes(8)));
            $customer->setPasswordHash(password_hash(sprintf('%sTyme%d%d', $customer->getPasswordSalt(), $customer->getZone(), $customer->getCode()), PASSWORD_DEFAULT));
            $customer->setPriceList($csvCustomerPriceList);
            $customer->setDeleted(false);
            $customer->setSellerCode($csvCustomerSellerCode);
            $customer->setUpdatedAt(self::$updatedAt);
            Customer::createUpdateCustomer($customer);
        }

        fclose($fd);

        $unUpdatedCustomers = Customer::getUnupdatedCustomers(self::$updatedAt);

        /** @var Customer $customer */
        foreach ($unUpdatedCustomers as $customer) {
            $customer->setDeleted(true);
            Customer::updateCustomer($customer);
        }
    }

    public static function importSellersFromCsvFile(): void
    {
        self::$updatedAt = date('Y-m-d H:i:s', strtotime('+3 day'));
        Logger::log('INFO', 'Importing sellers from csv file' . self::$updatedAt);
        Logger::log('INFO', self::$updatedAt);

        $sellerCSVFile = self::$basePath . "/writable/files/vendedor.csv";

        if (!file_exists($sellerCSVFile)) {
            Logger::log('ERROR', 'File ' . $sellerCSVFile . ' does not exist');
            throw new ApiException('File ' . $sellerCSVFile . ' does not exist');
        }

        $fd = fopen($sellerCSVFile, "r");

        if ($fd === false) {
            Logger::log('ERROR', 'Error opening file ' . $sellerCSVFile);
            throw new ApiException("Error opening file " . $sellerCSVFile);
        }

        while (($row = fgetcsv($fd, null, ";")) !== false) {
            $csvSellerCode = $row[0];
            $csvSellerName = $row[1];
            $csvSellerEmail = $row[3];
            $seller = new Seller();
            $seller->setCode($csvSellerCode);
            $seller->setName($csvSellerName);
            $seller->setEmail($csvSellerEmail);
            $seller->setDeleted(false);
            $seller->setUpdatedAt(self::$updatedAt);
            Seller::createOrUpdate($seller);
        }

        fclose($fd);

        $unUpdatedSellers = Seller::getOutdatedSellers(self::$updatedAt);

        /** @var Seller $seller */
        foreach ($unUpdatedSellers as $seller) {
            $seller->setDeleted(true);
            Seller::updateSeller($seller);
        }

        Logger::log('INFO', 'Finished importing sellers from csv file');
    }

    public static function importBalancesFromCsvFile(): void
    {
        self::$updatedAt = date('Y-m-d H:i:s', strtotime('+3 day'));
        Logger::log('INFO', 'truncating payments table ' . self::$updatedAt);

        $conn = Connection::getConn();
        $conn->real_query("TRUNCATE TABLE payments");

        Logger::log('INFO', 'Importing balances from csv file' . self::$updatedAt);
        Logger::log('INFO', self::$updatedAt);

        $balancesCSVFile = self::$basePath . "/writable/files/saldos.csv";

        if (!file_exists($balancesCSVFile)) {
            Logger::log('ERROR', 'File ' . $balancesCSVFile . ' does not exist');
            throw new ApiException('File ' . $balancesCSVFile . ' does not exist');
        }

        $fd = fopen($balancesCSVFile, "r");

        if ($fd === false) {
            Logger::log('ERROR', 'Error opening file ' . $balancesCSVFile);
            throw new ApiException("Error opening file " . $balancesCSVFile);
        }

        $rowCount = 0;
        $paymentsCSV = null;
        $customerCSV = null;

        while (($row = fgetcsv($fd, null, ";")) !== false) {
            if (!$rowCount) {
                $rowCount++;
                continue;
            }

            if ($row[0] === '1') {
                $customerCSV = $row;
            }

            if ($row[0] === '2') {
                $paymentsCSV[] = $row;
            }

            if ($row[0] === '3') {
                // Procesar cliente y sus pagos
                if ($customerCSV) {
                    $customerZone = $customerCSV[1];
                    $customerCode = $customerCSV[2];
                    $sellerCode = $customerCSV[3];

                    $customer = new Customer();
                    $customer->setZone($customerZone);
                    $customer->setCode($customerCode);

                    if (!empty($paymentsCSV)) {
                        foreach ($paymentsCSV as $paymentData) {
                            $typeReceipt = $paymentData[1];
                            $numberReceipt = $paymentData[3];
                            $dateReceipt = $paymentData[4];
                            $subtotalReceipt = $paymentData[6];
                            $ivaReceipt = $paymentData[7];
                            $totalReceipt = $paymentData[8];
                            $balanceReceipt = $paymentData[9];
                            $conditionSale = $paymentData[11];
                            $delayReceipt = $paymentData[12];
                            $balanceAccumulated = $paymentData[10];

                            $payment = new Payment(
                                $customerCode,
                                $sellerCode,
                                $typeReceipt,
                                $numberReceipt,
                                $dateReceipt,
                                $subtotalReceipt,
                                $totalReceipt,
                                $balanceReceipt,
                                $ivaReceipt,
                                $conditionSale,
                                $delayReceipt,
                                $balanceAccumulated
                            );

                            $payment->save();
                        }
                    } else {
                        Logger::log('WARNING', 'No paymentsCSV found to process for customer ' . $customerCode);
                    }
                } else {
                    Logger::log('ERROR', 'No customerCSV found before processing payments.');
                }

                $customerCSV = null;
                $paymentsCSV = [];
            }
        }

        fclose($fd);
    }
}
