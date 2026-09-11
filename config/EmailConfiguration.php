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
    public const SMTP_USERNAME = 'noreply@greendor.com.ar';

    /**
     * The password for SMTP authentication.
     */
    public const SMTP_PASSWORD = 'NT:gg;C^2:Qw';

    /**
     * The name of the sender.
     */
    public const SENDER_NAME = 'Greendor';

    /**
     * The sender email address.
     */
    public const SENDER_EMAIL = self::SMTP_USERNAME;

    public const VENTAS_EMAIL = 'ventas@greendor.com.ar';
    public const VENTAS_NAME = 'Ventas Greendor';
}

