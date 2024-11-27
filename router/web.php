<?php

use Basri\Router\View;

$router->get('/', function() {
    return View::make('welcome', [
        'name' => 'Hakkı Bulut'
    ]);
});

$router->get('/users', function() {
    return "kullanıcılıar";
});