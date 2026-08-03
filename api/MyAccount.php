<?php

namespace api;

use api\exceptions\ApiException;
use Exception;
use filters\SessionFilter;
use helpers\Request;
use helpers\Response;
use JsonException;
use models\Account;
use models\Customer;

/**
 *
 */
abstract class MyAccount {
    /**
     * @return void
     * @throws ApiException
     */
    public static function getMyAccount(): void {
        SessionFilter::validateApiSession();

        $account = unserialize($_SESSION['account']);

        if ($account instanceof Customer)
            Response::append('customer', $account);
        else Response::append('account', $account);

        Response::setCode(200);
    }

    /**
     * @throws ApiException
     * @throws JsonException
     * @throws Exception
     */
    public static function updateMyAccount(): void {
        SessionFilter::validateApiSession();

        $data = Request::getJson();

        /** @var Account $account */
        $account = unserialize($_SESSION['account']);

        if ($account instanceof Customer)
            throw new ApiException('Esta accion no se puede realizar', 403);

        if (!empty($data->name))
            $account->setName($data->name);

        if (!empty($data->email) && !filter_var($data->email, FILTER_VALIDATE_EMAIL))
            throw new ApiException('El email no es valido', 400);

        if (!empty($data->email)) {
            if (Account::getAccountByEmail($data->email))
                throw new ApiException('El email no esta disponible', 400);

            $account->setEmail($data->email);
            $account->setVerifiedEmail(false);
        }

        if (!empty($data->password) && !empty($data->newPassword)) {
            if (!password_verify($account->getPasswordSalt() . $data->password, $account->getPasswordHash()))
                throw new ApiException('La contraseña incorrecta', 403);

            $account->setPasswordSalt(base64_encode(random_bytes(9)));
            $account->setPasswordHash(password_hash($account->getPasswordSalt() . $data->newPassword, PASSWORD_DEFAULT));
        }

        $_SESSION['account'] = serialize($account);

        Account::updateAccount($account);

        Response::append('account', $account);

        Response::setCode(200);
    }
}