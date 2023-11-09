<?php

namespace App\Controllers;

use App\Services\AuthService;
use Core\Interfaces\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;

class AuthController {

  private ContainerInterface $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  public function logIn(ServerRequestInterface $req, ResponseInterface $resp) {
    $body = $req->getParsedBody();

    $login ??= $body['login'];
    $password ??= $body['password'];

    if ($login === null || $password === null) {
      return $resp->withStatus(400);
    }

    $service = new AuthService();
    $user = $service->login($login, $password);

    if ($user === null) {
      return $resp->withStatus(400);
    }

    $body = $resp->getBody();
    $body->write(json_encode($user->getSafetyData()));

    setcookie('Authorization', "Bearer {$user->token}", time() + (int) env('TOKEN_TTL'), '/');
    return $resp->withBody($body);
  }

}