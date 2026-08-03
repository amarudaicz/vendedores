<?php

namespace Services;

class PricingService
{
    private HttpService $http;

    public function __construct()
    {
        $this->http = new HttpService('https://sandbox.api.payulatam.com');
    }

    public function obtenerPreciosPorTarjeta(string $keyTarjeta, int $amount)
    {
        $pricing = $this->http->get('/payments-api/rest/v4.9/pricing', ['query' => [
            'paymentMethod' => $keyTarjeta,
            'amount' => $amount
        ]]);

        return $pricing;
    }
}
