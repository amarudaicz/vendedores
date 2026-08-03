<?php

namespace helpers;

abstract class Logger {
    /**
     *
     */
    const STD_OUT = 'php://stdout';

    /**
     * @var string
     */
    private static string $logDestination = self::STD_OUT;

    /**
     * Configure the log destination.
     *
     * @param string $destination Path to the log file or 'php://stdout'
     *
     * @return void
     */
    public static function configure(string $destination): void {
        self::$logDestination = $destination;
    }

    /**
     * Log a message with a given level.
     *
     * @param string $level   Log level (e.g., INFO, ERROR)
     * @param string $message Log message
     *
     * @return void
     */
    public static function log(string $level, string $message): void {
        $timestamp = date('Y-m-d H:i:s');

        // Format: [TIMESTAMP] LEVEL: MESSAGE
        $logLine = sprintf("[%s] %s: %s\n", $timestamp, $level, $message);

        // Write the log to the configured destination
        file_put_contents(self::$logDestination, $logLine, FILE_APPEND);
    }
}