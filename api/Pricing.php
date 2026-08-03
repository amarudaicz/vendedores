<?php

namespace api;

use helpers\Response;
use Services\PricingService;

class Pricing
{

    private PricingService $pricingService;

    public function __construct()
    {
        $this->pricingService = new PricingService();
    }

    public function obtenerPreciosPorTarjeta()
    {
        $keyTarjeta = $_GET['paymentMethod'] ?? '';
        $amount = $_GET['amount'] ?? '';
        
        $pricing = $this->pricingService->obtenerPreciosPorTarjeta($keyTarjeta, $amount);
        Response::append('pricing', $pricing);
        Response::setCode(200);
    }
}
