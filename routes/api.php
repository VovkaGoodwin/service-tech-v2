<?php

use Slim\Routing\RouteCollectorProxy as Group;
use \App\Controllers as C;

return function (\Slim\App $app) {
  $app->post('/login', [C\AuthController::class, 'logIn']);

  $app->group('/api', function (Group $api) {
    $api->group('/admin', function (Group $admin) {
      $admin->post('/user', [C\UserController::class, 'createUser']);
      $admin->delete('/user/{id}', [C\UserController::class, 'deleteUser']);
    })->add(\Core\Middleware\AdminMiddleware::class);

    $api->group('/switch', function (Group $switch) {
      $switch->get('', [C\SwitchController::class, 'findSwitch']);
      $switch->get('/state', [ C\SwitchController::class, 'rebootPort' ]);
      $switch->get('/counters', [ C\SwitchController::class, 'clearCounters' ]);
    });

  })->add(\Core\Middleware\AuthMiddleware::class);
};