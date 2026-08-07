<?php

use api\Pricing;

if (isset($router)) {
    $router->setBasePath('/api/v1');
    $router->set404NotFound('api\Api::error404');

    // Home contact
    $router->post('/contact', 'api\Contact::sendContact');

    // Authentication Endpoints
    $router->post('/accounts/sign-up', 'api\Authentication::signUp');
    $router->post('/accounts/sign-in', 'api\Authentication::signIn');
    $router->post('/accounts/recovery', 'api\Authentication::recovery');
    $router->post('/accounts/sign-out', 'api\Authentication::signOut');
    $router->post('/accounts/customers/sign-in', 'api\Authentication::customerSignIn');

    // My Account Management Endpoints
    $router->get('/accounts/my-account', 'api\MyAccount::getMyAccount');
    $router->post('/accounts/my-account', 'api\MyAccount::updateMyAccount');

    // Account Management Endpoints
    $router->get('/accounts', 'api\Accounts::getAccounts');
    $router->delete('/accounts/(\d+)', 'api\Accounts::deleteAccount');

    // Authorized Email Management Endpoints
    $router->get('/accounts/authorized-emails', 'api\AuthorizedEmails::getAuthorizedEmails');
    $router->post('/accounts/authorized-emails', 'api\AuthorizedEmails::createAuthorizedEmail');
    $router->delete('/accounts/authorized-emails/(\d+)', 'api\AuthorizedEmails::deleteAuthorizedEmail');

    // Product API
    $router->get('/products', 'api\Products::getProducts');
    $router->get('/products/([\d-]+)', 'api\Products::getProduct');
    $router->post('/products/([\d-]+)', 'api\Products::updateProduct');
    $router->get('/products/dolar', 'api\Products::getDolar');

    // Cotización API (Dollar Exchange Rate)
    $router->get('/cotizacion/dolar', 'api\Cotizaciones::getDolar');
    $router->put('/cotizacion/dolar', 'api\Cotizaciones::setDolar');

    // Promotion API
    $router->get('/promotions', 'api\Promotions::getPromotions');
    $router->post('/promotions', 'api\Promotions::createPromotion');
    $router->get('/promotions/(\d+)', 'api\Promotions::getPromotion');
    $router->post('/promotions/(\d+)', 'api\Promotions::updatePromotion');
    $router->delete('/promotions/(\d+)', 'api\Promotions::deletePromotion');

    // Customer API
    $router->get('/customers', 'api\Customers::getCustomers');
    $router->get('/customers/(\d+)', 'api\Customers::getCustomer');

    // Image API
    $router->get('/images', 'api\Images::getImages');
    $router->post('/images', 'api\Images::createImage');
    $router->get('/images/(\d+)', 'api\Images::getImage');
    $router->delete('/images/(\d+)', 'api\Images::deleteImage');

    // Log API
    $router->get('/log', 'api\Log::getLog');

    // Settings API
    $router->post('/settings/csv', 'api\Settings::updateDatabase');

    // Orders API
    $router->get('/orders', 'api\Orders::getOrders');
    $router->post('/orders', 'api\Orders::createOrder');
    $router->get('/orders/(\d+)', 'api\Orders::getOrder');
    $router->get('/orders/(\d+)/csv', 'api\Orders::getOrderCsv');
    $router->post('/orders/(\d+)', 'api\Orders::updateOrder');
    $router->put('/orders/(\d+)/items', 'api\Orders::updateOrderItems');
    $router->put('/orders/(\d+)/status', 'api\Orders::updateOrderStatus');
    $router->delete('/orders/(\d+)', 'api\Orders::deleteOrder');

    //SELLER API
    //products
    $router->get('/sellers/products', 'api\Sellers::searchProducts');
    //customers
    $router->get('/sellers/customers', 'api\Sellers::searchCustomers');
    //orders
    $router->post('/sellers/orders', 'api\Sellers::postOrder');
    $router->get('/sellers/orders', 'api\OrdersSellers::getOrders');
    $router->get('/sellers/all', 'api\Sellers::getSellers');
    $router->get('/sellers/stats', 'api\Sellers::getStats');

    //AUTH SELELRS
    $router->post('/sellers/auth', 'api\AuthSellers::signIn');
    $router->post('/sellers/auth/sign-up', 'api\AuthSellers::signUp');
    $router->get('/sellers/auth/sign-out', 'api\AuthSellers::signOut');
    $router->get('/sellers/auth/is-logged', 'api\AuthSellers::authenticate');
    $router->post('/sellers/auth/change-password', 'api\AuthSellers::changePassword');

    //SELLER DOLLAR RATE
    $router->get('/sellers/dolar', 'api\Sellers::getDolar');
    $router->put('/sellers/dolar', 'api\Sellers::updateDolar');

    //BALANCES!!!
    $router->get('/payments', 'api\Payments::getPayments');


    // $router->post('/pagos', 'api\pagos::crear');

    $router->get('/pricing', function () {
        $controller = new Pricing();
        $controller->obtenerPreciosPorTarjeta();
    });

}
  