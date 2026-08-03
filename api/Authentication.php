<?php

namespace api;

use Exception;
use JsonException;
use models\Account;
use helpers\Request;
use helpers\Response;
use api\exceptions\ApiException;
use models\AuthorizedEmail;
use models\Connection;
use models\Customer;

/**
 *
 */
abstract class Authentication {
    /**
     * @return void
     * @throws ApiException
     * @throws JsonException
     * @throws Exception
     */
    public static function signUp(): void {
        if (!empty($_SESSION['account']))
            throw new ApiException('Forbidden. You are already logged in.', 403);

        Connection::getConn()->begin_transaction();

        $data = Request::getJson();

        if (empty($data->name))
            throw new ApiException('Name is required', 400);

        if (empty($data->email))
            throw new ApiException('Email is required', 400);

        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL))
            throw new ApiException('Email format is invalid', 400);

        if (empty($data->password))
            throw new ApiException('Password is required', 400);

        $account = Account::getAccountByEmail($data->email);

        if (!empty($account))
            throw new ApiException('The email is already in use', 409);

        $authorizedEmail = AuthorizedEmail::getByEmail($data->email);

        if (empty($authorizedEmail))
            throw new ApiException('The email is not authorized', 403);

        $account = new Account();
        $account->setName($data->name);
        $account->setEmail($authorizedEmail->getEmail());
        $account->setPasswordSalt(base64_encode(random_bytes(9)));
        $account->setPasswordHash(password_hash($account->getPasswordSalt() . $data->password, PASSWORD_DEFAULT));
        $account->setGoogleSignIn(false);
        $account->setVerifiedEmail(false);
        $account->setProfileImage('');
        $account->setDeleted(false);
        $account->setCreatedAt(date('Y-m-d H:i:s'));
        $account->setUpdatedAt(date('Y-m-d H:i:s'));

        Account::createAccount($account);
        AuthorizedEmail::delete($authorizedEmail);

        Connection::getConn()->commit();

        $_SESSION['timestamp'] = time();
        $_SESSION['account'] = serialize($account);

        Response::setCode(204);
    }

    /**
     * @return void
     * @throws ApiException
     * @throws JsonException
     */
    public static function signIn(): void {

        $data = Request::getJson();

        if (empty($data->email))
            throw new ApiException('Email is required', 400);

        if (empty($data->password))
            throw new ApiException('Password is required', 400);

        $account = Account::getAccountByEmail($data->email);

        if (!$account)
            throw new ApiException('Invalid credentials', 401);

        if ($account->isDeleted())
            throw new ApiException('Account is already deleted', 409);

        if ($account->isGoogleSignIn())
            throw new ApiException('This account cannot be accessed through this sign-in method. Please use Google sign-in.', 401);

        if (!password_verify($account->getPasswordSalt() . $data->password, $account->getPasswordHash()))
            throw new ApiException('Invalid credentials', 401);

        $_SESSION['timestamp'] = time();
        $_SESSION['account'] = serialize($account);

        Response::setCode(204);
    }

    /**
     * @return void
     * @throws ApiException
     * @throws JsonException
     */
    public static function customerSignIn(): void {

        $data = Request::getJson();

        $customer = Customer::getCustomerByDNI($data->dni);

        if (empty($customer))
            throw new ApiException('Invalid credentials', 401);

        if ($customer->isDeleted())
            throw new ApiException('Customer is already deleted', 409);

        if (!password_verify($customer->getPasswordSalt() . $data->password, $customer->getPasswordHash()))
            throw new ApiException('Invalid credentials', 401);

        $_SESSION['timestamp'] = time();
        $_SESSION['account'] = serialize($customer);

        Response::append('customer', $customer);

        Response::setCode(200);
    }

    /**
     * @return void
     * @throws ApiException
     */
    public static function recovery(): void {
        if (!empty($_SESSION['account']))
            throw new ApiException('Forbidden. You are already logged in.', 403);

        throw new ApiException('Not implemented', 503);
    }

    /**
     * @return void
     * @throws ApiException
     */
    public static function signOut(): void {
        if (empty($_SESSION['account']))
            throw new ApiException('Forbidden. You need to be logged in first.', 403);

        $_SESSION = [];

        session_regenerate_id(true);

        session_destroy();

        Response::setCode(204);
    }
}