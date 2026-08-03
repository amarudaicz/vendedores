<?php

namespace helpers;

/**
 *
 */
class Response {
    /**
     * @var int
     */
    private static int $timestamp = 0;

    /**
     * @var int
     */
    private static int $code = 0;

    /**
     * @var string
     */
    private static string $message = '';

    /**
     * @var array
     */
    private static array $data = [];

    /**
     *
     */
    private function __construct() {}

    /**
     * @return int
     */
    public static function getTimestamp(): int {
        return self::$timestamp;
    }

    /**
     * @return int
     */
    public static function getCode(): int {
        return self::$code;
    }

    /**
     * @return string
     */
    public static function getMessage(): string {
        return self::$message;
    }

    /**
     * @return array
     */
    public static function getData(): array {
        return self::$data;
    }

    /**
     * @param int $timestamp
     *
     * @return void
     */
    public static function setTimestamp(int $timestamp): void {
        self::$timestamp = $timestamp;

    }

    /**
     * @param int $code
     *
     * @return void
     */
    public static function setCode(int $code): void {
        self::$code = $code;

    }

    /**
     * @param string $message
     *
     * @return void
     */
    public static function setMessage(string $message): void {
        self::$message = $message;

    }

    /**
     * @param array $data
     *
     * @return void
     */
    public static function setData(array $data): void {
        self::$data = $data;

    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public static function append(string $key, mixed $value): void {
        self::$data[$key] = $value;
    }

    /**
     * @return void
     */
    public static function send(): void {
        header('Content-type: application/json; charset=utf-8');
        echo json_encode([
            "timestamp" => self::$code,
            "code" => self::$code,
            "message" => self::$message,
            "data" => self::$data
        ]);
    }
}