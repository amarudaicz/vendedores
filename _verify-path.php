<?php
require __DIR__ . '/vendor/autoload.php';
$base = rtrim(dirname(__DIR__), '/\\');
$folder = $base . \services\OrderStatusService::PEDIDOS_DIR;
echo $folder . PHP_EOL;
echo 'is_dir: ' . (is_dir($folder) ? 'SI' : 'NO') . PHP_EOL;
