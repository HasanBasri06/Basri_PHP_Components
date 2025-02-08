<?php

use App\Models\User;
use Basri\Router\Router;
use Dotenv\Dotenv;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);


require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/config.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$router = new Router;

include_once __DIR__ . '/router/web.php';

$router->dispatch();