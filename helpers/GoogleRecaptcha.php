<?php

namespace helpers;

use api\exceptions\ApiException;
use JsonException;

/**
 *
 */
abstract class GoogleRecaptcha {
    /**
     * @var string[]
     */
    private static array $domains = ["essencedubai.com.ar", "essencedubai.com.ar", "tymerosario.local"];

    /**
     * @param string $token
     * @param string $action
     *
     * @return void
     * @throws ApiException
     * @throws JsonException
     */
    public static function validate(string $token, string $action): void {
        $curl = curl_init(\config\GoogleRecaptcha::VERIFY_ENDPOINT);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            "secret" => \config\GoogleRecaptcha::SECRET_KEY,
            "response" => $token
        ]);

        $response = curl_exec($curl);

        if (!$response)
            throw new ApiException('Hubo un error al validar el recaptcha. Intente nuevamente mas tarde.', 403);

        $response = json_decode($response, null, 512, JSON_THROW_ON_ERROR);

        if (!$response->success)
            throw new ApiException('Hubo un error al validar el recaptcha. Intente nuevamente mas tarde.', 403);

        if ($response->score < 0.8)
            throw new ApiException('Hubo un error al validar el recaptcha. Intente nuevamente mas tarde.', 403);

        if ($response->action !== $action)
            throw new ApiException('Hubo un error al validar el recaptcha. Intente nuevamente mas tarde.', 403);

        if (!in_array($response->hostname, self::$domains))
            throw new ApiException('Hubo un error al validar el recaptcha. Intente nuevamente mas tarde.', 403);
    }
}