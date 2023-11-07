<?php
return function (\Slim\App $app) {
  $app->get('/', [ \App\Controllers\StaticController::class, 'renderSite' ]);
};