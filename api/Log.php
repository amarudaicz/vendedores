<?php

namespace api;

use helpers\Response;

/**
 *
 */
abstract class Log {
    /**
     * @return void
     */
    public static function getLog(): void {
        $log = \models\Log::getLog();

        Response::append('log', $log);

        Response::setCode(200);
    }
}