<?php

use Basri\Router\Router;
use Basri\Storage\Storage;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Basri/Storage/Storage.php';
require_once __DIR__ . '/Basri/Router/Router.php';

$router = new Router;

$router->get('/', function() {
    return "otomatik deploy çalıştı";
});

$router->dispatch();