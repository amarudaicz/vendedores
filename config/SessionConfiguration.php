<?php

namespace config;

/**
 * SessionConfiguration is an abstract class that provides constant attributes for session configuration options.
 */
abstract class SessionConfiguration {
    /**
     * The session name.
     */
    public const SESSION_NAME = 'sid';

    /**
     * The session cookie domain.
     */
    public const SESSION_COOKIE_DOMAIN = SiteConfiguration::DOMAIN;

    /**
     * Indicates whether the session cookie should be secure (1 for true, 0 for false).
     */
    public const SESSION_COOKIE_SECURE = '0';

    /**
     * The session cookie lifetime in minutes.
     */
    public const SESSION_COOKIE_LIFETIME = '1440';

    /**
     * Indicates whether the session cookie should be accessible only through HTTP (1 for true, 0 for false).
     */
    public const SESSION_COOKIE_HTTPONLY = '1';

    /**
     * The session cookie path.
     */
    public const SESSION_COOKIE_PATH = '/';

    /**
     * The session cookie SameSite attribute value.
     */
    public const SESSION_COOKIE_SAMESITE = 'Lax';

    /**
     * Indicates whether strict mode should be used for session operations (1 for true, 0 for false).
     */
    public const SESSION_USE_STRICT_MODE = '0';

    /**
     * The session ID length in characters.
     */
    public const SESSION_SID_LENGTH = '50';

    /**
     * Set the session configuration options.
     */
    public static function setOptions(): void {
        ini_set('session.name', self::SESSION_NAME);
        ini_set('session.cookie_domain', self::SESSION_COOKIE_DOMAIN);
        ini_set('session.cookie_secure', self::SESSION_COOKIE_SECURE);
        ini_set('session.cookie_lifetime', self::SESSION_COOKIE_LIFETIME);
        ini_set('session.cookie_httponly', self::SESSION_COOKIE_HTTPONLY);
        ini_set('session.cookie_path', self::SESSION_COOKIE_PATH);
        ini_set('session.cookie_samesite', self::SESSION_COOKIE_SAMESITE);
        ini_set('session.use_strict_mode', self::SESSION_USE_STRICT_MODE);
        ini_set('session.sid_length', self::SESSION_SID_LENGTH);
    }
}
