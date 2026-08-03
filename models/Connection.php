<?php

namespace models;

use mysqli;
use config\DatabaseConfiguration;

/**
 *
 */
abstract class Connection {
    /**
     * @var mysqli|null
     */
    private static mysqli|null $conn = null;

    /**
     *
     */
    private function __construct() {}

    /**
     * @return mysqli
     */
    public static function getConn(): mysqli {
        if (!self::$conn) {
            self::$conn = new mysqli(
                DatabaseConfiguration::DB_HOST,
                DatabaseConfiguration::DB_USERNAME,
                DatabaseConfiguration::DB_PASSWORD,
                DatabaseConfiguration::DB_NAME,
                DatabaseConfiguration::DB_PORT
            );

            mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);

            self::$conn->set_charset('UTF8');
        }

        return self::$conn;
    }
}
