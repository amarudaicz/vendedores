<?php

namespace api;

use filters\SessionFilter;
use helpers\Response;
use models\Customer;
use models\Product;
use api\exceptions\ApiException;
use Exception;
use helpers\Logger;
use helpers\Notifications;
use helpers\Request;
use helpers\Session;
use models\Connection;
use models\Order;
use models\OrderItem;
use models\Seller;

abstract class Sellers
{

    /**
     * Buscar productos por query.
     *
     * @return void
     * @throws ApiException
     */
    public static function searchProducts(): void
    {
        SessionFilter::validateSellerSession();

        $query = $_GET['query'] ?? '';
        $customerPrice = $_GET['customer_price'] ?? '';
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ?  (int) $_GET['perPage'] : 10;

      // Si el query está vacío o es '*', buscar todos los productos
        if (empty($query) || $query === '*') {
            $query = '';
        }

        // Obtener la cotización del vendedor logueado
        $seller = Session::get('account');
        $dolar = $seller->getDolar();

        $products = Product::searchByQueryAndCustomerPrice($query, $customerPrice, $page, $perPage, $dolar);

        Response::setData($products);
        Response::setCode(200);
    }

    /**
     * Buscar clientes por query.
     *
     * @return void
     * @throws ApiException
     */

    public static function searchCustomers(): void
    {
        SessionFilter::validateSellerSession();

        $query = $_GET['query'] ?? '';
        $seller = Session::get('account');

        $isAdmin = $seller->getIsAdmin();
        
        // Si el usuario es admin y se pasa un sellerCode en la URL, lo usamos.
        // Si no es admin, usamos su propio código.
        if ($isAdmin) {
            $sellerCode = isset($_GET['sellerCode']) && $_GET['sellerCode'] !== '' ? (int)$_GET['sellerCode'] : null;
        } else {
            $sellerCode = $seller->getCode();
        }
        
        $customers = Customer::searchByQuery($sellerCode, $query);

        Response::append('customers', $customers);
        Response::setCode(200);
    }


    public static function postOrder(): void
    {

        SessionFilter::validateSellerSession();
        $seller = Session::get('account');

        $data = Request::getJson();
        $customer = $data->customer;

        if (empty($data->items))
            throw new ApiException('Agrega productos al pedido', 400);

        Connection::getConn()->begin_transaction();

        $order = new Order();
        if (!empty($data->note))
            $order->setNote($data->note);

        if (!empty($data->paymentMethod))
            $order->setPaymentMethod($data->paymentMethod);

        $order->setCreatedAt(date('Y-m-d H:i:s'));
        $order->setUpdatedAt(date('Y-m-d H:i:s'));

        //SI CREA UNA ORDEN MANUAL EL VENDEDOR
        $order->setCustomerCode($customer->code);
        $order->setCustomerZone($customer->zone);
        $order->setCotizacion($seller->getDolar());

        $order->setGuestId(null);
        Order::createOrder($order, $seller->getCode());

        $orderItems = [];

        foreach ($data->items as $item) {
            if (empty($item->product->code)) {
                throw new Exception('Código de producto requerido', 400);
            }
            // Se asume que los datos del producto ya vienen en $item->product
            $orderItem = new OrderItem();
            $orderItem->setDescription($item->product->name); // Usa directamente el nombre del producto
            $orderItem->setPrice($item->product->price); // Usa directamente el precio
            $orderItem->setQuantity($item->quantity ?: 1);
            $orderItem->setProductCode($item->product->code);
            $orderItem->setOrderId($order->getId());

            OrderItem::createOrderItem($orderItem);
            $orderItems[] = $orderItem;
        }

        Connection::getConn()->commit();

        Response::append('orderId', $order->getId());

        Response::setCode(200);
    }


    public static function getOrders(): void
    {
        SessionFilter::validateSellerSession();

        $seller = Session::get('account');

        $isAdmin = $seller->getIsAdmin();

        $filters = [];
        $filters['search'] = $_GET['search'] ?? null;
        $filters['status'] = $_GET['status'] ?? null;
        $filters['dateFrom'] = $_GET['dateFrom'] ?? null;
        $filters['dateTo'] = $_GET['dateTo'] ?? null;
        $filters['customer_code'] = $_GET['customer_code'] ?? null;
        
        $sellerCodeFilter = null;

        if ($isAdmin && isset($_GET['sellerCode'])) {
            $sellerCodeFilter = $_GET['sellerCode'] === '' ? null : (int)$_GET['sellerCode'];
        }else{
            if(!$isAdmin){
                $sellerCodeFilter = $seller->getCode();
            }
        }

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = !empty($_GET['perPage']) ? (int)$_GET['perPage'] : 10;
 
        error_log("sellerCodeFilter: " . $sellerCodeFilter);
        
        $paginatedResult = Order::getOrdersBySeller($sellerCodeFilter, $filters, $page, $perPage);
 
        $orders = $paginatedResult['orders'];

        // Obtener estadísticas filtradas
        $stats = Seller::getStats($sellerCodeFilter, $filters);
        
        Response::append('orders', $orders);
        Response::append('stats', $stats);
        Response::append('totalPages', $paginatedResult['totalPages']);
        Response::append('total', $paginatedResult['total']);
        Response::setCode(200);
    }



    public static function getStats(): void
    {
        SessionFilter::validateSellerSession();

        $seller = Session::get('account');
        $stats = Seller::getStats($seller->getCode());

        Response::append('stats', $stats);
        Response::setCode(200);
    }


    public static function getDolar(): void
    {
        SessionFilter::validateSellerSession();

        $seller = Session::get('account');
        error_log(json_encode($seller));
        $dolar = $seller->getDolar();

        Response::append('dolar', $dolar);
        Response::setCode(200);
    }

    public static function updateDolar(): void
    {
        SessionFilter::validateSellerSession();

        $data = Request::getJson();
        $dolar = $data->dolar ?? null;

        if ($dolar === null) {
            throw new ApiException('El valor del dólar es requerido', 400);
        }

        if ($dolar <= 0) {
            throw new ApiException('El valor del dólar debe ser mayor a 0', 400);
        }

        $seller = Session::get('account');
        $conn = Connection::getConn();

        $stmt = $conn->prepare("UPDATE vendedor SET vendedor_dolar = ?, vendedor_updated_at = ? WHERE vendedor_id = ?");
        $updatedAt = date('Y-m-d H:i:s');
        $sellerId = $seller->getId();

        $stmt->bind_param('dsi', $dolar, $updatedAt, $sellerId);
        $result = $stmt->execute();

        if (!$result) {
            throw new ApiException('Error al actualizar el valor del dólar', 500);
        }

        // Update seller object in session
        $seller->setDolar((float)$dolar);
        $_SESSION['account'] = serialize($seller);

        Response::append('success', true);
        Response::append('dolar', $dolar);
        Response::setCode(200);
    }

    public static function getSellers(): void
    {
        SessionFilter::validateSellerSession();
        $sellers = Seller::getAll();
        Response::setData($sellers);
        Response::setCode(200);
    }
}
