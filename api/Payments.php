<?php


namespace api;

use api\exceptions\ApiException;
use helpers\Logger;
use helpers\Response;
use models\Payment;

abstract class Payments
{

    public static function getPayments()
    {
        $customer_code = $_GET['id'];
        $customer_zone = $_GET['zone'];

        if (!isset($customer_code)) {
            return new ApiException('id es requerido', 400);
        }

        if (!isset($customer_zone)) {
            return new ApiException('zone es requerido', 400);
        }

        $payments = Payment::getPayments($customer_code, $customer_zone);

        Response::setData(($payments));
    }
}
