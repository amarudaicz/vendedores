<?php

namespace filters;

use Exception;
use api\exceptions\ApiException;
use helpers\Session;
use models\Account;
use models\Seller;

abstract class SessionFilter {
    /**
     * @return void
     * @throws ApiException
     */
    public static function validateApiSession(): void {
        if (empty($_SESSION['account'])){
            http_response_code(401);
            throw new ApiException('Authentication Required: Please log in to access this resource.', 401);
        }

        if (time() - $_SESSION['timestamp'] > 15 * 60) {
            $_SESSION = [];
            session_regenerate_id(true);
            session_destroy();
            http_response_code(401);
            throw new ApiException('Authentication Required: The session expired. Please, sign in again', 401);
        }

        if (time() - $_SESSION['timestamp'] > 5 * 60)
            session_regenerate_id(true);

        $_SESSION['timestamp'] = time();
    }

    /**
     * @return void
     */
    public static function validateWebSession(): void {
        if (empty($_SESSION['account'])) {
            header('Location: /app/auth/sign-in', true, 302);
            exit;
        }

        if (time() - $_SESSION['timestamp'] > 15 * 60) {
            $_SESSION = [];
            session_regenerate_id(true);
            session_destroy();
            header('Location: /app/auth/sign-in', true, 302);
            exit;
        }

        if (time() - $_SESSION['timestamp'] > 5 * 60)
            session_regenerate_id(true);

        $_SESSION['timestamp'] = time();
    }

    public static function validateSellerSession(): void{
        $seller = Session::get('account');

        if (($seller instanceof Account)){
            http_response_code(401);
            throw new ApiException('Tu session expiro o no es valida', 401);
        }
    }
}