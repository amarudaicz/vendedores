<?php

namespace filters;

use api\exceptions\ApiException;
use helpers\Session;
use models\Seller;

abstract class SessionFilter
{
    /**
     * Tiempo máximo de inactividad: 7 días.
     */
    private const SESSION_TIMEOUT = 7 * 24 * 60 * 60;

    /**
     * Intervalo de regeneración del ID de sesión: 5 minutos.
     */
    private const SESSION_REGENERATE_INTERVAL = 5 * 60;

    /**
     * Valida la sesión de API.
     *
     * @return void
     * @throws ApiException
     */
    public static function validateApiSession(): void
    {
        if (empty($_SESSION['account'])) {
            throw new ApiException(
                'Authentication Required: Please log in to access this resource.',
                401
            );
        }

        self::validateExpiration();
    }

    /**
     * Valida la sesión web.
     *
     * @return void
     */
    public static function validateWebSession(): void
    {
        if (empty($_SESSION['account'])) {
            header('Location: /app/auth/sign-in', true, 302);
            exit;
        }

        try {
            self::validateExpiration();
        } catch (ApiException $e) {
            header('Location: /app/auth/sign-in', true, 302);
            exit;
        }
    }

    /**
     * Comprueba la expiración y renueva la actividad de la sesión.
     *
     * @return void
     * @throws ApiException
     */
    private static function validateExpiration(): void
    {
        $now = time();

        $lastActivity = $_SESSION['timestamp'] ?? $now;

        // Expiración por inactividad.
        if ($now - $lastActivity > self::SESSION_TIMEOUT) {
            $_SESSION = [];

            session_destroy();

            throw new ApiException(
                'Authentication Required: The session expired. Please, sign in again.',
                401
            );
        }

        // Regeneración periódica del identificador.
        if ($now - $lastActivity > self::SESSION_REGENERATE_INTERVAL) {
            session_regenerate_id(true);
        }

        // Actualizar última actividad.
        $_SESSION['timestamp'] = $now;
    }

    /**
     * Valida que exista una sesión de vendedor.
     *
     * @return void
     * @throws ApiException
     */
    public static function validateSellerSession(): void
    {
        $seller = Session::get('account');

        if (!$seller instanceof Seller) {
            http_response_code(401);

            throw new ApiException(
                'Tu sesión expiró o no es válida.',
                401
            );
        }

        self::validateExpiration();
    }
}
