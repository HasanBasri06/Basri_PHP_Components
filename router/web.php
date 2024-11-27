<?php

use Basri\Router\View;

$router->get('/', function() {
    return View::make('welcome', [
        'name' => 'Hakkı Bulut'
    ]);
});

$router->get('/users', function() {
    echo "kullanıcılıar"; die;
});

$router->get('/deneme', function() {
    echo "Deneme"; die;
});