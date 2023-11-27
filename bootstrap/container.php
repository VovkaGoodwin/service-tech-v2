<?php
$container = \Core\Container::getInstance();

$container->register([
  \App\Services\AuthService::class => \App\Services\AuthService::class,
  \App\Services\UserService::class => \App\Services\UserService::class,
  \App\Services\HomeService::class => \App\Services\HomeService::class,
  \App\Services\SwitchService::class => \App\Services\SwitchService::class,
]);