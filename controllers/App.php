<?php

namespace controllers;

use Exception;
use filters\AccountFilter;
use filters\SessionFilter;

/**
 *
 */
abstract class App {
    /**
     * @return void
     */
    public static function signUp(): void {
        if (!empty($_SESSION['account'])) {
            header('Location: /app');
            return;
        }

        echo file_get_contents('public/pages/sign-up/sign-up.html');
    }

    /**
     * @return void
     */
    public static function signIn(): void {
        if (!empty($_SESSION['account'])) {
            header('Location: /app');
            return;
        }

        echo file_get_contents('public/pages/sign-in/sign-in.html');
    }

    /**
     * @return void
     */
    public static function recovery(): void {
        if (!empty($_SESSION['account'])) {
            header('Location: /app');
            return;
        }

        echo file_get_contents('public/pages/recovery/recovery.html');
    }

    /**
     * @return void
     */
    public static function signOut(): void {
        if (empty($_SESSION['account'])) {
            header('Location: /app/auth/sign-in');
            return;
        }

        unset($_SESSION['account']);

        $_SESSION = [];

        session_destroy();

        header('Location: /app/auth/sign-in');
    }

    /**
     * @return void
     * @throws Exception
     */
    public static function index(): void {
        SessionFilter::validateWebSession();

        AccountFilter::filterWebCustomerAccount();

        header('Location: /app/dashboard', true, 301);
    }

    /**
     * @return void
     * @throws Exception
     */
    public static function app(): void {
        SessionFilter::validateWebSession();

        AccountFilter::filterWebCustomerAccount();

        echo file_get_contents('public/pages/app/app.html');
    }

    /**
     * @return void
     * @throws Exception
     */
    public static function myAccount(): void {
        SessionFilter::validateWebSession();

        AccountFilter::filterWebCustomerAccount();

        echo file_get_contents('public/pages/my-account/my-account.html');
    }
}