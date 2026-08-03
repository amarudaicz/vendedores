<?php

use helpers\Logger;
use helpers\Notifications;
use services\OrderStatusService;

/**
 * Script CLI standalone para actualizar estados de pedidos.
 *
 * Pasa los pedidos CONFIRMADO a EN PROCESO cuando ya no existen en la
 * carpeta compartida ../writable/files/pedidos (fuera del proyecto).
 *
 * Uso: php order-status.php
 */

require_once __DIR__ . '/vendor/autoload.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

if (php_sapi_name() !== 'cli') {
    throw new RuntimeException('Este script solo puede ejecutarse desde la línea de comandos.');
}

if (!OrderStatusService::isScheduledTime()) {
    echo "OrderStatus time does not match\n";
    exit(0);
}

try {
    OrderStatusService::moveConfirmedToInProgress(dirname(__DIR__));
    echo "OrderStatus OK\n";
} catch (\Throwable $th) {
    $errorMessage = 'Error actualizando estados de pedidos: ' . $th->getMessage() . "\n" . $th->getTraceAsString();
    Logger::log('ERROR', $errorMessage);
    Notifications::notifyAdminError('Error en actualización de estados de pedidos', $errorMessage);
    exit(1);
}
