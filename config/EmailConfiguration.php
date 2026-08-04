<?php

namespace config;

/**
 * EmailConfiguration is an abstract class that provides constant attributes for email configuration.
 */
abstract class EmailConfiguration {
    /**
     * The SMTP host for the email server.
     */
    public const SMTP_HOST = 'smtp.hostinger.com';

    /**
     * The SMTP port for the email server.
     */
    public const SMTP_PORT = 465;

    /**
     * The username for SMTP authentication.
     */
    public const SMTP_USERNAME = 'noreply@nvd.com.ar';

    /**
     * The password for SMTP authentication.
     */
    public const SMTP_PASSWORD = 'Dxtz=Qb&X8';

    /**
     * The name of the sender.
     */
    public const SENDER_NAME = 'NVD Mayorista';

    /**
     * The sender email address.
     */
    public const SENDER_EMAIL = self::SMTP_USERNAME;

    public const VENTAS_EMAIL = 'ventas@nvd.com.ar';
    public const VENTAS_NAME = 'Ventas NVD';
}

