<?php

namespace api;

use api\exceptions\ApiException;
use filters\AccountFilter;
use filters\SessionFilter;
use helpers\Request;
use helpers\Response;
use JsonException;
use models\Account;
use models\AuthorizedEmail;

abstract class AuthorizedEmails {
    /**
     * @return void
     * @throws ApiException
     */
    public static function getAuthorizedEmails(): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $authorizedEmails = AuthorizedEmail::getAll();

        Response::append('authorizedEmails', $authorizedEmails);

        Response::setCode(200);
    }

    /**
     * @return void
     * @throws ApiException
     * @throws JsonException
     */
    public static function createAuthorizedEmail(): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $data = Request::getJson();

        if (empty($data->email))
            throw new ApiException('Email is required', 400);

        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL))
            throw new ApiException('Email is required', 400);

        $authorizedEmail = AuthorizedEmail::getByEmail($data->email);

        if (!empty($authorizedEmail))
            throw new ApiException('The email is already authorized', 409);

        $account = Account::getAccountByEmail($data->email);

        if (!empty($account))
            throw new ApiException('The email is already in used', 409);

        $authorizedEmail = new AuthorizedEmail();
        $authorizedEmail->setEmail($data->email);

        AuthorizedEmail::create($authorizedEmail);

        Response::append('authorizedEmail', $authorizedEmail);

        Response::setCode(200);
    }

    /**
     * @param int $authorizedEmailId
     *
     * @return void
     * @throws ApiException
     */
    public static function deleteAuthorizedEmail(int $authorizedEmailId): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $authorizedEmail = AuthorizedEmail::getById($authorizedEmailId);

        if (empty($authorizedEmail))
            throw new ApiException('The email does not exist', 404);

        AuthorizedEmail::delete($authorizedEmail);

        Response::setCode(204);
    }
}