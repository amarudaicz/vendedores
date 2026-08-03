<?php

namespace helpers;

use JsonException;

/**
 *
 */
abstract class Request {
    /**
     * @throws JsonException
     */
    public static function getJson(): mixed {
        return json_decode(file_get_contents('php://input'), null, 512, JSON_THROW_ON_ERROR);
    }
}