<?php

namespace api\exceptions;

use Exception;

/**
 *
 */
class ApiException extends Exception {
    /**
     * @var int
     */
    private int $customCode;

    /**
     * @param string $message
     * @param int    $code
     * @param int    $customCode
     */
    public function __construct(string $message = "", int $code = 0, int $customCode = 0) {
        parent::__construct($message, $code);
        $this->customCode = $customCode;
    }

    /**
     * @return int
     */
    public function getCustomCode(): int {
        return $this->customCode;
    }
}