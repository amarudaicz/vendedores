<?php

use config\ErrorConfiguration;
use helpers\Router;

include_once("vendor/autoload.php");

ErrorConfiguration::setOptions();

try {
    session_start();
    $router = new Router();
    include_once("routes/web.php");
    $router->run();
} catch (Throwable $e) {
    controllers\Controller::error500();
}
