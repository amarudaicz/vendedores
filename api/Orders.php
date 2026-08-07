<?php

namespace api;

use api\exceptions\ApiException;
use filters\AccountFilter;
use filters\SessionFilter;
use helpers\Logger;
use helpers\Notifications;
use helpers\OrderPdf;
use helpers\Request;
use helpers\Response;
use JsonException;
use helpers\Session;
use models\Account;
use models\Connection;
use models\Cotizacion;
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
            $customer = Customer::getCustomerByCode($order->getCustomerCode(), $order->getCustomerZone());

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

        $seller = Session::get('account');
        $order = Order::getOrderById($orderId);

        if (empty($order))
            throw new ApiException('Orden no encontrada', 404);

        $orderItems = OrderItem::getOrderItems($orderId);

        $customer = Customer::getCustomerByCode($order->getCustomerCode() ?? $order->getGuestId(), $order->getCustomerZone());

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
            $fileContent .= "6;\r\n";
            $fileContent .= "7;\r\n";
            $fileContent .= sprintf("8;%s\r\n", Cotizacion::getCotizacionById(1)->getValor());
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
            $fileContent .= "6;\r\n";
            $fileContent .= "7;\r\n";
            $fileContent .= sprintf("8;%s\r\n", $seller->getDolar() ? $seller->getDolar() : Cotizacion::getCotizacionById(1)->getValor());
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

        $filename = sprintf('../writable/files/pedidos/%08d.txt', $order->getId());

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
        $cotizacion = Cotizacion::getCotizacionById(1)->getValor();

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
        $order->setCustomerZone($customer->getZone());

        $order->setGuestId(null);
        $order->setCotizacion($cotizacion);

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

        $filename = sprintf('../writable/files/pedidos/%08d.txt', $order->getId());
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
        $fileContent .= "6;\r\n";
        $fileContent .= "7;\r\n";
        $fileContent .= sprintf("8;%s\r\n", $cotizacion);


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
        $cotizacion = Cotizacion::getCotizacionById(1)->getValor();

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
        if (!empty($data->email))
            $guest->setEmail($data->email);
        $guest->setCreatedAt(date('Y-m-d H:i:s'));
        Guest::createGuest($guest);

        $order = new Order();
        if (!empty($data->note))
            $order->setNote($data->note);
        if (!empty($data->paymentMethod))
            $order->setPaymentMethod($data->paymentMethod);
        $order->setCreatedAt(date('Y-m-d H:i:s'));
        $order->setUpdatedAt(date('Y-m-d H:i:s'));
        $order->setCustomerCode(null);
        $order->setGuestId($guest->getId());
        $order->setCotizacion($cotizacion);
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

        $filename = sprintf('../writable/files/pedidos/%08d.txt', $order->getId());
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
        $fileContent .= "6;\r\n";
        $fileContent .= "7;\r\n";
        $fileContent .= sprintf("8;%s\r\n", $cotizacion);

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

        $seller = Session::get('account');
        $dolar = $seller->getDolar();

        $data = Request::getJson();

        if (!isset($data->status)) {
            throw new ApiException('El campo status es requerido', 400);
        }

        $order = Order::getOrderById($orderId);

        if (empty($order))
            throw new ApiException('Orden no encontrada', 404);

        if (!in_array($data->status, [Order::STATUS_PENDING, Order::STATUS_FINALIZED, Order::STATUS_NOT_REALIZED, Order::STATUS_IN_PROGRESS, Order::STATUS_CONFIRMED]))
            throw new ApiException('El estado de la orden es inválido', 400);

        if (!Order::canChangeStatus($order->getStatus(), $data->status)) {
            http_response_code(422);
            throw new ApiException(sprintf(
                'No se puede cambiar el estado de la orden de "%s" a "%s"',
                Order::getStatusLabel($order->getStatus()),
                Order::getStatusLabel($data->status)
            ), 400);
        }

        $order->setStatus($data->status);
        $order->setUpdatedAt(date('Y-m-d H:i:s'));

        Order::updateOrder($order);

        $statusLabel = Order::getStatusLabel($data->status);
        $toEmail = '';
        $customerName = '';
        $customer = null;
        $guest = null;
        $pdfPath = null;



        if (!empty($order->getCustomerCode())) {
            error_log('customerCODE: ' . $order->getCustomerCode());
            $customer = Customer::getCustomerByCode($order->getCustomerCode(), $order->getCustomerZone());
            error_log('customer: ' . json_encode($customer));
            if (!empty($customer)) {
                $customerName = $customer->getName() ?? $customerName;
                $toEmail = $customer->getEmail() ?? '';
            }
        } elseif (!empty($order->getGuestId())) {
            $guest = Guest::getGuestById($order->getGuestId());
            if (!empty($guest)) {
                $customerName = $guest->getName() ?? $customerName;
                $toEmail = $guest->getEmail() ?? '';
            }
        }

        error_log('Email: ' . $customer->getEmail());
        // Crear archivo TXT solo cuando la orden pase a estado "confirmed"
        if ($data->status === Order::STATUS_CONFIRMED) {
            $orderItems = OrderItem::getOrderItems($orderId);

            if (!empty($order->getCustomerCode())) {
                if (!empty($customer)) {
                    $filename = sprintf('../writable/files/pedidos/%08d.txt', $order->getId());
                    $fileContent = sprintf("1;%08d\r\n", $order->getId());
                    $fileContent .= sprintf("2;%d;%d;%s\r\n", $customer->getZone(), $customer->getCode(), $customer->getName());

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
                    $fileContent .= "6;\r\n";
                    $fileContent .= "7;\r\n";
                    $fileContent .= sprintf("8;%s\r\n", $dolar);

                    if (file_put_contents($filename, $fileContent) === false)
                        throw new ApiException('No se pudo guardar el archivo', 500);
                }
            }

            // Generar PDF de la Nota de Pedido para adjuntarlo al mail
            try {
                $pdfContent = OrderPdf::generate($order, $orderItems, $customer, $guest);
                $pdfPath = sprintf('../writable/files/pedidos/%08d.pdf', $order->getId());
                if (file_put_contents($pdfPath, $pdfContent) === false)
                    $pdfPath = null;
            } catch (\Throwable $e) {
                error_log('No se pudo generar el PDF del pedido #' . $order->getId() . ': ' . $e->getMessage());
                $pdfPath = null;
            }
        }

        if (!empty($toEmail)) {
            try {
                Notifications::sendOrderStatusUpdate($order, $toEmail, $statusLabel, $customerName, $pdfPath);
            } catch (\Exception $e) {
                error_log('No se pudo enviar el mail de cambio de estado para el pedido #' . $order->getId() . ': ' . $e->getMessage());
            }
        } else {
            error_log('No se pudo enviar el mail de cambio de estado para el pedido #' . $order->getId() . ': el cliente no tiene email registrado');
        }

        if (!empty($pdfPath) && file_exists($pdfPath)) {
            @unlink($pdfPath);
        }

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

        if (empty($data->items)) {
            throw new ApiException('La orden debe tener al menos un producto', 400);
        }

        $order = Order::getOrderById($orderId);

        if (empty($order))
            throw new ApiException('Orden no encontrada', 404);

        // Solo permitir modificar órdenes pendientes o finalizadas
        // Comprobación flexible de mayúsculas/minúsculas por si acaso
        $currentStatus = strtoupper($order->getStatus());
        if ($currentStatus !== 'PENDING')
            throw new ApiException('No se puede modificar una orden que ya se procesó (Estado: ' . $order->getStatus() . ').', 400);

        Connection::getConn()->begin_transaction();

        try {
            // 1. Obtener items originales y DEVOLVER stock al inventario
            $originalItems = OrderItem::getOrderItems($orderId);
            foreach ($originalItems as $origItem) {
                $product = Product::getProductByCode($origItem->getProductCode());
                if ($product) {
                    $restoredStock = $product->getStock() + $origItem->getQuantity();
                    Product::updateStock($product->getCode(), $restoredStock);
                }
            }

            // 2. Eliminar items anteriores
            OrderItem::deleteOrderItems($orderId);

            // 3. Procesar nuevos items y DESCONTAR stock
            $newOrderItems = [];
            foreach ($data->items as $item) {
                $product = Product::getProductByCode($item->product_code);

                if (empty($product))
                    throw new \Exception('Producto no encontrado: ' . ($item->product_code ?? 'Sin código'), 404);

                $orderItem = new OrderItem();
                $orderItem->setDescription($item->description ?? $product->getName());
                $orderItem->setPrice($item->price ?? $product->getPrice());
                $orderItem->setQuantity($item->quantity);
                $orderItem->setProductCode($product->getCode());
                $orderItem->setOrderId($orderId);
                OrderItem::createOrderItem($orderItem);
                $newOrderItems[] = $orderItem;

                // Descontar del stock
                $currentProduct = Product::getProductByCode($product->getCode()); // Refrescar stock
                $newStock = $currentProduct->getStock() - $item->quantity;
                Product::updateStock($product->getCode(), $newStock);
            }

            // 4. Actualizar fecha de modificación en la base de datos
            $order->setUpdatedAt(date('Y-m-d H:i:s'));
            Order::updateOrder($order);

            Connection::getConn()->commit();

            Response::append('order', $order);
            Response::setCode(200);
        } catch (\Throwable $e) {
            Connection::getConn()->rollback();
            throw new ApiException($e->getMessage(), 500);
        }
    }
}
