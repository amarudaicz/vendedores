<?php

namespace controllers;

abstract class Controller {
    /**
     * @return void
     */
    public static function error404(): void {
        header('HTTP/ 404 Not Found');
        echo file_get_contents('public/pages/404/404.html');
    }

    /**
     * @return void
     */
    public static function error500(): void {
        header('HTTP/ 500 Internal Server Error');
        echo file_get_contents('public/pages/500/500.html');
    }
}