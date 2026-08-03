<?php

namespace config;

use Exception;

/**
 *
 */
abstract class ErrorConfiguration {
    /**
     * @var bool
     */
    public static bool $displayErrors = false;

    /**
     * @var int
     */
    public static int $errorReporting = 0;

    /**
     * @var string
     */
    public static string $errorHandler = 'config\ErrorConfiguration::errorHandler';

    /**
     * @throws Exception
     */
    public static function errorHandler(int $code, string $message): void {
        throw new Exception($message, $code);
    }

    /**
     * @return void
     */
    public static function setOptions(): void {
        ini_set('display_errors', self::$displayErrors ? 1 : 0);
        error_reporting(self::$errorReporting);
        ini_set('error_reporting', self::$errorReporting);
        set_error_handler(self::$errorHandler);
    }
}