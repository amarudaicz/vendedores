<?php

namespace controllers;

use Exception;

/**
 *
 */
abstract class Sellers {
    /**
     * @return void
     * @throws Exception
     */
    public static function index(): void {
        echo file_get_contents('public/pages/sellers/app/dist/browser/index.html');
    }


}
