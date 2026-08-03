<?php

namespace filters;

use api\exceptions\ApiException;
use controllers\Controller;
use Exception;
use models\Customer;
use models\Seller;

abstract class AccountFilter {
    /**
     * @return void
     * @throws ApiException
     */
    public static function filterApiCustomerAccount(): void {
        if (empty($_SESSION['account'])) return;

        $account = unserialize($_SESSION['account']);

        if ($account instanceof Customer)
            throw new ApiException('No tienes acceso a este recurso.', 403);
    }

    /**
     * @throws Exception
     */
    public static function filterWebCustomerAccount(): void {
        if (empty($_SESSION['account'])) return;

        $account = unserialize($_SESSION['account']);

        if ($account instanceof Customer) {
            Controller::error404();
            exit;
        }

        // if ($account instanceof Seller) {
        //     Controller::error404();
        //     exit;
        // }

          if ($account instanceof Seller) {
            unset($_SESSION['account']);
            session_destroy();
            header('Location: /app/auth/sign-in');
            exit;
        }
    }
} 
