<?php

namespace api;

use Exception;
use filters\AccountFilter;
use filters\SessionFilter;
use helpers\Request;
use JsonException;
use models\AuthorizedEmail;
use models\Account;
use helpers\Response;
use api\exceptions\ApiException;

/**
 *
 */
abstract class Accounts {
    /**
     * @return void
     * @throws ApiException
     */
    public static function getAccounts(): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        Response::append('accounts', Account::getAccounts());

        Response::setCode(200);
    }

    /**
     * @param int $accountId
     *
     * @return void
     * @throws ApiException
     */
    public static function deleteAccount(int $accountId): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $account = Account::getAccountById($accountId);

        if (empty($accountId))
            throw new ApiException('The account does not exist', 404);

        if ($account->getId() == 1)
            throw new ApiException('The account is an administrator', 403);

        if ($account->getId() === unserialize($_SESSION['account'])->getId())
            throw new ApiException('You cannot delete you own account', 403);

        Account::deleteAccount($account);

        Response::setCode(204);
    }
}