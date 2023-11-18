<?php

namespace App\Controllers;

use App\Services\AuthService;
use Core\Interfaces\ContainerInterface;
use Core\Traits\MsgpackTrait;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Rakit\Validation\Validator;
use Slim\Psr7\Cookies;

class AuthController {

  use MsgpackTrait;
  private ContainerInterface $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  public function logIn(ServerRequestInterface $req, ResponseInterface $resp) {
    $body = $req->getParsedBody();


    $validator = new Validator();
    $validated = $validator->validate($body, [
      'login' => 'required|max:24',
      'password' => 'required|max:25'
    ]);

    if ($validated->fails()) {
      return $resp->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
    }

    $data = $validated->getValidData();

    /** @var AuthService $service */
    $service = $this->container->make(AuthService::class);
    $user = $service->login($data['login'], $data['password']);

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

  public function logOut(ServerRequestInterface $req, ResponseInterface $resp) {
    $cookie = new Cookies();
    $cookie->set('Authorization', [
      'value' => '',
      'expires' => time() - 1,
    ]);
    return $resp
      ->withStatus(StatusCodeInterface::STATUS_NO_CONTENT)
      ->withHeader('Set-Cookie', $cookie->toHeaders());
  }

}