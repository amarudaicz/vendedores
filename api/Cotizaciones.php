<?php

namespace api;

use api\exceptions\ApiException;
use filters\AccountFilter;
use filters\SessionFilter;
use helpers\Request;
use helpers\Response;
use JsonException;
use models\Cotizacion;

/**
 *
 */
abstract class Cotizaciones {
    /**
     * @return void
     * @throws ApiException
     */
    public static function getDolar(): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $cotizacion = Cotizacion::getCotizacionById(1);

        if (empty($cotizacion))
            throw new ApiException('La cotización no existe', 404);

        Response::append('cotizacion', $cotizacion);

        Response::setCode(200);
    }

    /**
     * @return void
     * @throws ApiException
     * @throws JsonException
     */
    public static function setDolar(): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $data = Request::getJson();

        if (!isset($data->valor))
            throw new ApiException('Valor requerido', 400);

        if (!is_numeric($data->valor))
            throw new ApiException('El valor debe ser numérico', 400);

        if ($data->valor <= 0)
            throw new ApiException('El valor debe ser mayor que cero', 400);

        $cotizacion = Cotizacion::getCotizacionById(1);

        if (empty($cotizacion))
            throw new ApiException('La cotización no existe', 404);

        $cotizacion->setValor((float) $data->valor);

        Cotizacion::updateCotizacion($cotizacion);

        // Fetch updated record to return
        $cotizacion = Cotizacion::getCotizacionById(1);

        Response::append('cotizacion', $cotizacion);

        Response::setCode(200);
    }
}
