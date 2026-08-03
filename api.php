<?php

use helpers\Router;
use helpers\Response;
use config\ErrorConfiguration;
use api\exceptions\ApiException;

define('BASE_PATH', __DIR__);

include_once("vendor/autoload.php");

ErrorConfiguration::setOptions();

try {
    session_start();
    $router = new Router();
    include_once("routes/api.php");
    $router->run();
} catch (ApiException $e) {
    Response::setCode($e->getCode());
    Response::setMessage($e->getMessage());
} catch (Throwable $e) {
    Response::setCode(500);
    Response::setMessage('Hubo un error al procesar tu solicitud: ' . $e->getMessage());
}

Response::send();