<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);

use Basri\Router\Router;
use Basri\Storage\Storage;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Basri/Storage/Storage.php';
require_once __DIR__ . '/Basri/Router/Router.php';

$router = new Router;

$router->get('/', function() {
    return "otomatik deploy çalıştı";
});

$router->get('/users', function() {
    return "kullanıcılar alındı";
});

$router->post('/users', function() {
    return "kullanıcılar alındı";
});

$router->dispatch();