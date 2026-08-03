<?php
namespace helpers;
use helpers\Logger;


abstract class Session{

    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Obtiene un valor de la sesión
     * 
     * @param string $key Clave de la sesión
     * @return mixed|null Valor deserializado o null si no existe
     */
    public static function get(string $key): mixed
    {
        self::startSession();

        return !empty($_SESSION[$key]) ? unserialize($_SESSION[$key]) : null;
    }
}