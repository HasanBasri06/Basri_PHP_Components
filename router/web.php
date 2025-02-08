<?php

use App\Models\User;
use Basri\Router\Router;
use Basri\Router\View;

$router->get('/', function() {
    return View::make('welcome', [
        'name' => 'Hakkı Bulut'
    ]);
});

$router->get('/users', function() {
    (new User())->save([
        'name' => "John Doe",
        "email" => "johndoe@gmail.com",
        "password" => password_hash("123", PASSWORD_DEFAULT),
        "status" => 'active'
    ]);

    return View::make('users', ['users' => (new User())->get()]);
});


// $router->group('admin', function (Router $group) {
//     $group->get('dashboard', fn() => "Admin dashboard page");
//     $group->get('about/{id}', [HomeController::class, 'index']);
// });