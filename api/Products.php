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
} 