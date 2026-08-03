<?php

namespace api;

use api\exceptions\ApiException;
use helpers\Response;

/**
 *
 */
abstract class Api {
    /**
     * @return void
     */
    public static function error404(): void {
        Response::setMessage('The endpoint does not exist');
        Response::setCode(404);
    }
}