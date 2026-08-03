<?php

namespace api;

use Exception;
use filters\AccountFilter;
use filters\SessionFilter;
use models\Log;
use helpers\Response;
use models\Category;
use models\Connection;
use models\Customer;
use models\Product;
use models\Subcategory;
use api\exceptions\ApiException;
use helpers\Logger;
use helpers\Notifications;
use models\Payment;
use models\Seller;

/**
 *
 */
abstract class Settings
{
    private static string $updatedAt = '';

    /**
     * @return void
     * @throws ApiException
     */
    public static function updateDatabase(): void
    {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        self::$updatedAt = date('Y-m-d H:i:s');

        try {
            Connection::getConn()->begin_transaction();

            \services\SyncService::init(dirname(__DIR__), self::$updatedAt);
            \services\SyncService::runAllImportProcedures();

            $log = new Log();
            $log->setDescription('Actualizacion manual base de datos');
            $log->setCreatedAt(date('Y-m-d H:i:s'));
            Log::createLog($log);

            Connection::getConn()->commit();
            Response::setCode(204);
        } catch (\Throwable $th) {
            Connection::getConn()->rollback();
            $errorMessage = 'Error importing data: ' . $th->getMessage() . "\n" . $th->getTraceAsString();
            // Notifications::notifyAdminError('Error en SINCRONIZACIÓN de datos', $errorMessage);
            Response::setCode(500);
            Response::setMessage($th->getMessage());
        }
    }
}
 