<?php

use Slim\Routing\RouteCollectorProxy as Group;

return function (\Slim\App $app) {
  $app->post('/login', [ \App\Controllers\AuthController::class, 'logIn' ]);

  $app->group('/api', function (Group $api) {

  });
};