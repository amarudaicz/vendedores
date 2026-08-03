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
abstract class Orders
{
    /**
     * Obtiene las órdenes para el vendedor logueado, con filtros y paginación.
     * @return void
     * @throws ApiException
     */
    public static function getOrders(): void
    {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        if (!empty($_GET['year']))
            $orders = Order::getOrdersByYear($_GET['year']);
        else
            $orders = Order::getOrders();

        Response::append('orders', $orders);

        Response::setCode(200);
    }
    /** 
     * @param int $orderId
     *
     * @return void
     * @throws ApiException
     */
    public static function getOrder(int $orderId): void
    {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $order = Order::getOrderById($orderId);

        if (empty($order))
            throw new ApiException('Orden no encontrada', 404);

        $orderItems = OrderItem::getOrderItems($orderId);

        if (!empty($order->getCustomerCode()))
            $customer = Customer::getCustomerByCode($order->getCustomerCode());

        if (!empty($order->getGuestId()))
            $guest = Guest::getGuestById($order->getGuestId());

        Response::append('customer', $customer ?? null);
        Response::append('guest', $guest ?? null);
        Response::append('items', $orderItems);
        Response::append('order', $order);

        Response::setCode(200);
    }

    /**
     * @param int $orderId
     * @return void
     * @throws ApiException
     */
    public static function getOrderCsv(int $orderId): void
    {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $order = Order::getOrderById($orderId);

        if (empty($order))
            throw new ApiException('Orden no encontrada', 404);

        $orderItems = OrderItem::getOrderItems($orderId);

        $customer = Customer::getCustomerByCode($order->getCustomerCode() ?? 0);

        if (!empty($customer)) {
            $fileContent = sprintf("1;%08d\r\n", $order->getId());
            $fileContent .= sprintf("2;%d;%d\r\n", $customer->getZone(), $customer->getCode());
            /** @var OrderItem $orderItem */
            foreach ($orderItems as $orderItem) {
                $fileContent .= sprintf(
                    "3;%s;%.3f;;0.00;%.3f\r\n",
                    $orderItem->getProductCode(),
                    $orderItem->getQuantity(),
                    $orderItem->getPrice()
                );
            }
            $fileContent .= sprintf("4;%s\r\n", $order->getNote());
            $fileContent .= sprintf("5;%s\r\n", $order->getPaymentMethod());
        }

        if (empty($customer)) {
            $fileContent = sprintf("1;%08d\r\n", $order->getId());
            $fileContent .= sprintf("2;99;99999999;%s\r\n", 'Anonimo');
            foreach ($orderItems as $orderItem) {
                $fileContent .= sprintf(
                    "3;%s;%.3f;;0.00;%.3f\r\n",
                    $orderItem->getProductCode(),
                    $orderItem->getQuantity(),
                    $orderItem->getPrice()
                );
            }
            $fileContent .= sprintf("4;%s\r\n", $order->getNote());
            $fileContent .= sprintf("5;%s\r\n", $order->getPaymentMethod());
        }

        Response::append('filename', sprintf("%05d.csv", $order->getId()));
        Response::append('csv', ('data:text/csv;base64,' . base64_encode($fileContent)));

        Response::setCode(200);
    }

    /**
     * @param int $orderId
     * @return void
     * @throws ApiException
     */
    public static function downloadCsv(int $orderId): void
    {
        $order = Order::getOrderById($orderId);

        if (empty($order))
            throw new ApiException('Orden no encontrada', 404);

        $filename = sprintf('writable/orders/%08d.txt', $order->getId());

        if (!file_exists($filename))
            throw new ApiException('Archivo no encontrado', 404);

        $content = file_get_contents($filename);

        $uriData = sprintf("data:text/csv;charset=utf8;base64,%s", base64_encode($content));

        Response::append('uri', $uriData);

        Response::setCode(200);
    }

    /**
     * @return void
     * @throws JsonException
     * @throws ApiException
     * @throws Exception
     */
    public static function createOrder(): void
    {
        $account = !empty($_SESSION['account']) ? unserialize($_SESSION['account']) : null;

        if (empty($account)) {
            self::createGuestOrder();
            return;
        }

        if ($account instanceof Account) {
            self::createGuestOrder();
            return;
        }

        /** @var Customer $customer */
        $customer = unserialize($_SESSION['account']);

        $data = Request::getJson();

        if (empty($data->items))
            throw new ApiException('El carrito esta vacio', 400);

        Connection::getConn()->begin_transaction();

        $order = new Order();
        if (!empty($data->note))
            $order->setNote($data->note);
        if (!empty($data->paymentMethod))
            $order->setPaymentMethod($data->paymentMethod);
        $order->setCreatedAt(date('Y-m-d H:i:s'));
        $order->setUpdatedAt(date('Y-m-d H:i:s'));

        //SI CREA UNA ORDEN MANUAL EL VENDEDOR 
        $order->setCustomerCode($customer->getCode());

        $order->setGuestId(null);

        Order::createOrder($order);

        $orderItems = [];

        foreach ($data->items as $item) {
            if (empty($item->productCode))
                throw new Exception('Código de producto requerido', 400);

            $product = Product::getProductByCode($item->productCode);

            if (empty($product))
                throw new Exception('Producto no encontrado', 404);

            $orderItem = new OrderItem();
            $orderItem->setDescription($product->getName());
            $orderItem->setPrice($product->getCustomerPrice($customer->getPriceList()));
            $orderItem->setQuantity($item->quantity);
            $orderItem->setProductCode($product->getCode());
            $orderItem->setOrderId($order->getId());
            OrderItem::createOrderItem($orderItem);
            $orderItems[] = $orderItem;

            // Descontar del stock
            $newStock = $product->getStock() - $item->quantity;
            Product::updateStock($product->getCode(), $newStock);
        }

        $filename = sprintf('writable/orders/%08d.txt', $order->getId());
        $fileContent = sprintf("1;%08d\r\n", $order->getId());
        $fileContent .= sprintf("2;%d;%d\r\n", $customer->getZone(), $customer->getCode());
        foreach ($orderItems as $orderItem) {
            $fileContent .= sprintf(
                "3;%s;%.3f;;0.00;%.3f\r\n",
                $orderItem->getProductCode(),
                $orderItem->getQuantity(),
                $orderItem->getPrice()
            );
        }
        $fileContent .= sprintf("4;%s\r\n", $order->getNote());
        $fileContent .= sprintf("5;%s\r\n", $order->getPaymentMethod());

        if (file_put_contents($filename, $fileContent) === false)
            throw new ApiException('No se pudo guardar el archivo', 500);

        Notifications::sendCustomerOrder($order, $filename, $customer->getName());

        Connection::getConn()->commit();

        Response::append('orderId', $order->getId());

        Response::setCode(200);
    }

    /**
     * @return void
     * @throws JsonException
     * @throws ApiException
     * @throws Exception
     */
    public static function createGuestOrder(): void
    {
        $data = Request::getJson();

        if (empty($data->name))
            throw new ApiException('El nombre es requerido', 400);

        if (empty($data->phone))
            throw new ApiException('El telefono es requerido', 400);

        if (empty($data->tin))
            throw new ApiException('El CUIT es requerido', 400);

        if (empty($data->location))
            throw new ApiException('La localidad es requerida', 400);

        if (empty($data->postalCode))
            throw new ApiException('El codigo postal es requerido', 400);

        Connection::getConn()->begin_transaction();

        $guest = new Guest();
        $guest->setName($data->name);
        $guest->setTin($data->tin);
        $guest->setPhone($data->phone);
        $guest->setLocation($data->location);
        $guest->setPostalCode($data->postalCode);
        $guest->setCreatedAt(date('Y-m-d H:i:s'));
        Guest::createGuest($guest);

        $order = new Order();
        if (!empty($data->cart->note))
            $order->setNote($data->cart->note);
        if (!empty($data->cart->paymentMethod))
            $order->setPaymentMethod($data->cart->paymentMethod);
        $order->setCreatedAt(date('Y-m-d H:i:s'));
        $order->setUpdatedAt(date('Y-m-d H:i:s'));
        $order->setCustomerCode(null);
        $order->setGuestId($guest->getId());
        Order::createOrder($order);

        $orderItems = [];

        foreach ($data->cart->items as $item) {
            if (empty($item->productCode))
                throw new Exception('Código de producto requerido', 400);

            $product = Product::getProductByCode($item->productCode);

            if (empty($product))
                throw new Exception('Producto no encontrado', 404);

            $orderItem = new OrderItem();
            $orderItem->setDescription($product->getName());
            $orderItem->setPrice($product->getPrice());
            $orderItem->setQuantity($item->quantity);
            $orderItem->setProductCode($product->getCode());
            $orderItem->setOrderId($order->getId());
            OrderItem::createOrderItem($orderItem);
            $orderItems[] = $orderItem;

            // Descontar del stock
            $newStock = $product->getStock() - $item->quantity;
            Product::updateStock($product->getCode(), $newStock);
        }

        $filename = sprintf('writable/orders/%08d.txt', $order->getId());
        $fileContent = sprintf("1;%08d\r\n", $order->getId());
        $fileContent .= sprintf("2;99;99999999;%s\r\n", trim($data->name));
        foreach ($orderItems as $orderItem) {
            $fileContent .= sprintf(
                "3;%s;%.3f;;0.00;%.3f\r\n",
                $orderItem->getProductCode(),
                $orderItem->getQuantity(),
                $orderItem->getPrice()
            );
        }
        $fileContent .= sprintf("4;%s\r\n", $order->getNote());
        $fileContent .= sprintf("5;%s\r\n", $order->getPaymentMethod());

        if (file_put_contents($filename, $fileContent) === false)
            throw new ApiException('No se pudo guardar el archivo', 500);

        Notifications::sendGuestOrder($order, $filename, [$data->name, $data->phone, $data->tin, $data->location, $data->postalCode]);

        Connection::getConn()->commit();

        Response::append('orderId', $order->getId());

        Response::setCode(200);
    }

    /**
     * @param int $orderId
     * @return void
     * @throws ApiException
     * @throws JsonException
     */
    public static function updateOrder(int $orderId): void
    {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $data = Request::getJson();

        $order = Order::getOrderById($orderId);

        if (empty($order))
            throw new ApiException('Orden no encontrada', 404);

        if (isset($data->status)) {
            if (!in_array($data->status, [Order::STATUS_PENDING, Order::STATUS_FINALIZED, Order::STATUS_NOT_REALIZED]))
                throw new ApiException('El estado de la orden es invalido', 400);
            $order->setStatus($data->status);
        }

        $order->setUpdatedAt(date('Y-m-d H:i:s'));

        Order::updateOrder($order);

        Response::append('order', $order);

        Response::setCode(200);
    }

    /**
     * Actualiza solo el estado de una orden (endpoint específico para cambios rápidos)
     * @param int $orderId
     * @return void
     * @throws ApiException
     */
    public static function updateOrderStatus(int $orderId): void
    {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $data = Request::getJson();

        if (!isset($data->status)) {
            throw new ApiException('El campo status es requerido', 400);
        }

        $order = Order::getOrderById($orderId);

        if (empty($order))
            throw new ApiException('Orden no encontrada', 404);

        if (!in_array($data->status, [Order::STATUS_PENDING, Order::STATUS_FINALIZED, Order::STATUS_NOT_REALIZED]))
            throw new ApiException('El estado de la orden es inválido', 400);

        $order->setStatus($data->status);
        $order->setUpdatedAt(date('Y-m-d H:i:s'));

        Order::updateOrder($order);

        Response::append('order', $order);
        Response::setCode(200);
    }

    /**
     * Actualiza los items de una orden (modificación)
     * @param int $orderId
     * @return void
     * @throws ApiException
     */
    public static function updateOrderItems(int $orderId): void
    {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

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