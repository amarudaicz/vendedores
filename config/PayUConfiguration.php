<?php

namespace config;

use Environment;
use PayU;
use SupportedLanguages;

abstract class PayUConfiguration
{
    protected static string $apiLogin;
    protected static string $apiKey;
    protected static string $merchantId;
    protected static string $accountId;
    protected static bool $sandbox;

    public static function init(): void
    {
        // leemos desde .env
        self::$apiLogin   = 'pRRXKOl8ikMmt9u';
        self::$apiKey     = '4Vj8eK4rloUd272L48hsrarnUA';
        self::$merchantId = '508029';
        self::$accountId  = '512322';
        self::$sandbox    = true;

        self::initializeEnvironment();
        error_log(self::$accountId);
    }

    protected static function initializeEnvironment(): void
    {
        if (self::$sandbox) {
            Environment::setPaymentsCustomUrl('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi');
            Environment::setReportsCustomUrl('https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi');
        } else {
            Environment::setPaymentsCustomUrl('https://api.payulatam.com/payments-api/4.0/service.cgi');
            Environment::setReportsCustomUrl('https://api.payulatam.com/reports-api/4.0/service.cgi');
        }

        // credenciales
        PayU::$apiLogin   = self::$apiLogin;
        PayU::$apiKey     = self::$apiKey;
        PayU::$merchantId = self::$merchantId;
        PayU::$language = SupportedLanguages::ES;
    }
}

