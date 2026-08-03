<?php

namespace api;

use config\PayUConfiguration;
use helpers\Response;
use PayUCountries;
use PayUParameters;
use PayUPayments;

PayUConfiguration::init();

abstract class Pagos
{

    public function __construct() {}


    public static function crear()
    {

        try {
            $reference = "payment44_test_00000001";
            $value = "104300";

            $parameters = array(

                PayUParameters::ACCOUNT_ID => "512322",
                PayUParameters::REFERENCE_CODE => $reference,
                PayUParameters::DESCRIPTION => "payment test",

                // -- Valores --
                PayUParameters::VALUE => $value,
                PayUParameters::CURRENCY => "ARS",
                PayUParameters::INSTALLMENTS_NUMBER => "3",

                // -- Comprador --
                PayUParameters::BUYER_ID => "1",
                PayUParameters::BUYER_NAME => "First name and second buyer  name",
                PayUParameters::BUYER_EMAIL => "buyer_test@test.com",
                PayUParameters::BUYER_CONTACT_PHONE => "7563126",
                PayUParameters::BUYER_DNI => "5415668464654",
                PayUParameters::BUYER_STREET => "Av Centenario 837",
                PayUParameters::BUYER_STREET_2 => "5555487",
                PayUParameters::BUYER_CITY => "San Isidro",
                PayUParameters::BUYER_STATE => "Buenos Aires",
                PayUParameters::BUYER_COUNTRY => "AR",
                PayUParameters::BUYER_POSTAL_CODE => "000000",
                PayUParameters::BUYER_PHONE => "7563126",


                // -- Pagador --
                PayUParameters::PAYER_ID => "1",
                PayUParameters::PAYER_NAME => "First name and second payer name",
                PayUParameters::PAYER_EMAIL => "payer_test@test.com",
                PayUParameters::PAYER_CONTACT_PHONE => "7563126",
                PayUParameters::PAYER_DNI => "5415668464654",
                PayUParameters::PAYER_DOCUMENT_TYPE => "DNI",
                PayUParameters::PAYER_STREET => "Av Centenario 837",
                PayUParameters::PAYER_STREET_2 => "5555487",
                PayUParameters::PAYER_CITY => "San Isidro",
                PayUParameters::PAYER_STATE => "Buenos Aires",
                PayUParameters::PAYER_COUNTRY => "AR",
                PayUParameters::PAYER_POSTAL_CODE => "000000",
                PayUParameters::PAYER_PHONE => "7563126",

                // -- Datos de la tarjeta de crédito --
                // Ingresa aquí el número de la tarjeta de crédito
                PayUParameters::CREDIT_CARD_NUMBER => "4517730000000000 ",
                // Ingresa aquí la fecha de expiración de la tarjeta de crédito
                PayUParameters::CREDIT_CARD_EXPIRATION_DATE => "2027/12",
                // Ingresa aquí el código de seguridad de la tarjeta de crédito
                PayUParameters::CREDIT_CARD_SECURITY_CODE => "777",
                // Ingresa aquí el nombre de la tarjeta de crédito
                PayUParameters::PAYMENT_METHOD => "VISA",

                // Ingresa aquí el número de cuotas.
                PayUParameters::INSTALLMENTS_NUMBER => "1",
                // Ingresa aquí el nombre del país.
                PayUParameters::COUNTRY => PayUCountries::AR,

                // Device Session ID
                PayUParameters::DEVICE_SESSION_ID => "vghs6tvkcle931686k1900o6e1",
                // IP del pagador
                PayUParameters::IP_ADDRESS => "127.0.0.1",
                // Cookie de la sesión actual
                PayUParameters::PAYER_COOKIE => "pt1t38347bs6jc9ruv2ecpv7o2",
                // User agent de la sesión actual
                PayUParameters::USER_AGENT => "Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0"
            );

            // Petición de "Autorización y captura"
            $response = PayUPayments::doAuthorizationAndCapture($parameters);

            // Puedes obtener las propiedades en la respuesta
            if ($response) {
                $response->transactionResponse->orderId;
                $response->transactionResponse->transactionId;
                $response->transactionResponse->state;
                if ($response->transactionResponse->state == "PENDING") {
                    $response->transactionResponse->pendingReason;
                }
                $response->transactionResponse->responseCode;
            }

            Response::setData(['pago' => $response]);
        } catch (\Throwable $e) {
            Response::setData([
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function obtener() {}
}
