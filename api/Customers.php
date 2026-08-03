<?php

namespace api;

use filters\AccountFilter;
use filters\SessionFilter;
use helpers\Response;
use models\Customer;
use api\exceptions\ApiException;

/**
 *
 */
abstract class Customers {
    /**
     * @return void
     * @throws ApiException
     */
    public static function getCustomers(): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $customers = Customer::getCustomers();

        Response::append('customers', $customers);

        Response::setCode(200);
    }

    /**
     * @param int $customerCode
     *
     * @return void
     * @throws ApiException
     */
    public static function getCustomer(int $customerCode): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $customer = Customer::getCustomerByCode($customerCode);

        if (empty($customer))
            throw new ApiException('El cliente no existe', 404);

        Response::append('customer', $customer);

        Response::setCode(200);
    }
}