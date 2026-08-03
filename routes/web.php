<?php

if (isset($router)) {
    $router->get('/', 'controllers\Home::home');
    $router->set404NotFound('controllers\Controller::error404');
}