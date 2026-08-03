<?php
header('Access-Control-Allow-Origin:  http://localhost:4200');  // Cambia esto al dominio de tu frontend
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    //code...
    // Servir archivos estáticos directamente si existen
    if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $file = __DIR__ . urldecode($path);
    
    if (is_file($file)) {
        return false; // Dejar que PHP embebido sirva el archivo directamente
    } 
} 

// Reglas de redirección equivalentes al .htaccess
$requestUri = $_SERVER["REQUEST_URI"];

// Redirección de rutas que comienzan con `/api/v1` a `api.php`
if (preg_match('/^\/api\/v1/', $requestUri)) {
    require_once __DIR__ . '/api.php';
    exit;
}

// Todas las demás rutas redirigen a `index.php`
require_once __DIR__ . '/index.php';

} catch (\Throwable $th) {
    error_log($th->getMessage());
}