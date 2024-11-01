<?php 

use Basri\Router\Router;

require_once __DIR__ . '/Basri/Router/Router.php';

$router = new Router();

$router->get('/', function() {
    return 'adwad';
});
$router->post('/save-user', fn() => 'Kullanıcı kayıt edildi');

$router->group('api', function(Router $router) {
    $router->get('users', fn() => 'kullanıcılar', 'kullanicilar');
    $router->post('deneme', fn() => 'deneme kullanıcılar');
    $router->get('users/{id}', fn($id) => $id . '\'li kullanıcı');
});

$router->put('update-user', fn() => ['name' => 'Hasan Basri']);

$router->dispatch();