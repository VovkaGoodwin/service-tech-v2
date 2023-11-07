<?php

namespace App\Controllers;

use App\Services\AuthService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;

class AuthController {

  public function logIn(ServerRequestInterface $req, ResponseInterface  $resp) {
    $body = $req->getParsedBody();

    $login ??= $body['login'];
    $password ??= $body['password'];

    if ($login === null || $password === null) {
      return $resp->withStatus(400);
    }

    $service = new AuthService();
    $token = $service->login($login, $password);

    if ($token === null) {
      return $resp->withStatus(400);
    }

    return $resp->withHeader('Authorization', "Bearer: {$token}");

  }

}