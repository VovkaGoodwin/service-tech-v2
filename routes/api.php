<?php

use Slim\Routing\RouteCollectorProxy as Group;

return function (\Slim\App $app) {
  $app->post('/login', [ \App\Controllers\AuthController::class, 'logIn' ]);

  $app->group('/api', function (Group $api) {
    $api->group('/admin', function (Group $admin) {
      $admin->post('/user', [ \App\Controllers\UserController::class, 'createUser' ]);
    })->add(\Core\Middleware\AdminMiddleware::class);
  })->add(\Core\Middleware\AuthMiddleware::class);
};