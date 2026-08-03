<?php

namespace services;

use helpers\Logger;
use models\Connection;
use models\Order;

class OrderStatusService
{
    /**
     * Horarios en los que se permite ejecutar la actualización de estados.
     */
    const RUN_TIMES = ['06:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'];

    /**
     * Directorio donde se depositan los pedidos listos para el sistema externo.
     */
    const PEDIDOS_DIR = '/writable/files/pedidos';

    /**
     * Verifica si la hora actual coincide con los horarios de ejecución.
     *
     * @return bool
     */
    public static function isScheduledTime(): bool
    {
        return in_array(date('H:i'), self::RUN_TIMES, true);
    }

    /**
     * Pasa los pedidos CONFIRMADO a EN PROCESO cuando ya no existen en la carpeta de pedidos.
     *
     * @param string $basePath
     * @return void
     */
    public static function moveConfirmedToInProgress(string $basePath): void
    {
        $folder = rtrim($basePath, '/\\') . self::PEDIDOS_DIR;

        if (!is_dir($folder)) {
            throw new \RuntimeException('Pedidos directory does not exist: ' . $folder);
        }

        $orderIdsInFolder = self::getOrderIdsFromFolder($folder);

        $confirmedOrderIds = self::getConfirmedOrderIds();

        $toUpdate = array_diff($confirmedOrderIds, $orderIdsInFolder);

        if (empty($toUpdate)) {
            Logger::log('INFO', 'OrderStatusService: no hay pedidos CONFIRMADO para pasar a EN PROCESO');
            return;
        }

        foreach ($toUpdate as $orderId) {
            $order = new Order();
            $order->setId((int) $orderId);
            $order->setStatus(Order::STATUS_IN_PROGRESS);
            Order::updateOrder($order);
            Logger::log('INFO', sprintf('OrderStatusService: pedido %d pasado a EN PROCESO', $orderId));
        }

        Logger::log('INFO', sprintf('OrderStatusService: %d pedido(s) actualizados de CONFIRMADO a EN PROCESO', count($toUpdate)));
    }

    /**
     * Obtiene los IDs de orden presentes como archivos .txt en la carpeta.
     *
     * @param string $folder
     * @return array
     */
    private static function getOrderIdsFromFolder(string $folder): array
    {
        $files = scandir($folder);
        $ids = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (preg_match('/^(\d+)\.txt$/i', $file, $matches)) {
                $ids[] = (int) $matches[1];
            }
        }

        return $ids;
    }

    /**
     * Obtiene los IDs de pedidos en estado CONFIRMED.
     *
     * @return array
     */
    private static function getConfirmedOrderIds(): array
    {
        $conn = Connection::getConn();

        $query = "SELECT orden_id AS id FROM ordenes WHERE orden_status = ?";
        $status = Order::STATUS_CONFIRMED;

        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $status);
        $stmt->execute();

        $result = $stmt->get_result();

        $ids = [];

        while (($row = $result->fetch_assoc())) {
            $ids[] = (int) $row['id'];
        }

        $result->free();

        return $ids;
    }
}