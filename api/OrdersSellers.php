<?php

namespace api;

use api\exceptions\ApiException;
use filters\AccountFilter;
use filters\SessionFilter;
use helpers\Notifications;
use helpers\Request;
use helpers\Response;
use JsonException;
use helpers\Session;
use models\Account;
use models\Connection;
use models\Customer;
use models\Guest;
use models\Order;
use models\OrderItem;
use models\Product;
use models\Seller;
use PHPMailer\PHPMailer\Exception;

/**
 *
 */
abstract class OrdersSellers {
    /**
     * Obtiene las órdenes para el vendedor logueado, con filtros y paginación.
     * @return void
     * @throws ApiException
     */
    public static function getOrders(): void {
        SessionFilter::validateSellerSession();
 
        /** @var \models\Seller $seller */
        $seller = Session::get('account');
        $isAdmin = false;
        
   
        $isAdmin = $seller->getIsAdmin(); 

        $filters = [];
        $filters['search'] = $_GET['search'] ?? null;
        $filters['status'] = $_GET['status'] ?? null;
        $filters['dateFrom'] = $_GET['dateFrom'] ?? null;
        $filters['dateTo'] = $_GET['dateTo'] ?? null;
        $filters['customer_code'] = $_GET['customer_code'] ?? null;
        $filters['pedidosWeb'] = $isAdmin && !empty($_GET['pedidosWeb']) ? true : false;
        
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
    

    /**
     * Actualiza los items de una orden (modificación)
     * @param int $orderId
     * @return void
     * @throws ApiException
     */
    public static function updateOrderItems(int $orderId): void {
        SessionFilter::validateSellerSession();

        $data = Request::getJson();

        $order = Order::getOrderById($orderId);

        if (empty($order))
            throw new ApiException('Orden no encontrada', 404);

        // Solo permitir modificar órdenes pendientes
        if ($order->getStatus() !== Order::STATUS_PENDING)
            throw new ApiException('No se puede modificar una orden que ya se procesó.', 400);

        Connection::getConn()->begin_transaction();

        try {
            // Obtener items originales para ajustar stock
            $originalItems = OrderItem::getOrderItems($orderId);

            // Crear un mapa de items originales
            $originalMap = [];
            foreach ($originalItems as $item) {
                $originalMap[$item->getProductCode()] = $item->getQuantity();
            }

            // Eliminar items anteriores
            OrderItem::deleteOrderItems($orderId);

            // Procesar nuevos items
            foreach ($data->items as $item) {
                $product = Product::getProductByCode($item->product_code);

                if (empty($product))
                    throw new Exception('Producto no encontrado', 404);

                $orderItem = new OrderItem();
                $orderItem->setDescription($item->description);
                $orderItem->setPrice($item->price);
                $orderItem->setQuantity($item->quantity);
                $orderItem->setProductCode($product->getCode());
                $orderItem->setOrderId($orderId);
                OrderItem::createOrderItem($orderItem);

                // Ajustar stock
                $originalQty = $originalMap[$item->product_code] ?? 0;
                $qtyDifference = $originalQty - $item->quantity;
                $newStock = $product->getStock() + $qtyDifference;
                Product::updateStock($product->getCode(), $newStock);
            }

            // Actualizar fecha de modificación
            $order->setUpdatedAt(date('Y-m-d H:i:s'));
            Order::updateOrder($order);

            Connection::getConn()->commit();

            Response::append('order', $order);
            Response::setCode(200);
        } catch (Exception $e) {
            Connection::getConn()->rollback();
            throw new ApiException($e->getMessage(), 500);
        }
    }


  
}