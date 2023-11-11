<?php

namespace App\Controllers;

use App\Services\AuthService;
use Core\Interfaces\ContainerInterface;
use Core\Traits\MsgpackTrait;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Cookies;

class AuthController {

  use MsgpackTrait;
  private ContainerInterface $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  public function logIn(ServerRequestInterface $req, ResponseInterface $resp) {
    $body = $req->getParsedBody();

    $login ??= $body['login'];
    $password ??= $body['password'];

    if ($login === null || $password === null) {
      return $resp->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
    }

    $service = new AuthService();
    $user = $service->login($login, $password);

    if ($user === null) {
      return $resp->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
    }

    $body = $resp->getBody();
    $body->write($this->encode([
      'user' => $user->getSafetyData(),
    ]));
    $cookie = new Cookies();
    $cookie->set('Authorization', [
      'value' => "Bearer {$user->token}",
      'expires' => time() + (int) env('TOKEN_TTL'),
      'path' => '/',
      'httponly' => true
    ]);

    return $resp->withBody($body)->withHeader('Set-Cookie', $cookie->toHeaders());
  }

}