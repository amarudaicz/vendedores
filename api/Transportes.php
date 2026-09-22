<?php

namespace api;

use helpers\Response;
use models\Transporte;

abstract class Transportes {
    /**
     * Obtener todos los transportes disponibles.
     *
     * @return void
     */
    public static function getTransportes(): void {
        $transportes = Transporte::getAll();

        Response::setData($transportes);
        Response::setCode(200);
    }
}
