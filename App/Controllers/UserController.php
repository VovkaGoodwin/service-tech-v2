<?php

namespace App\Controllers;

use App\Services\UserService;
use Core\Traits\ContainerTrait;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rakit\Validation\Validator;

class UserController {
  use ContainerTrait;
  public function createUser(ServerRequestInterface $request, ResponseInterface $response) {
    $body = $request->getParsedBody();
    $valid = $this->validator->validate($body, [
      'login' => 'required|max:24',
      'password' => 'required|max:24',
      'firstName' => 'required|max:24',
      'lastName' => 'required|max:24',
      'phone' => 'required|max:12',
      'isAdmin' => 'boolean'
    ]);

    if ($valid->fails()) {
      return $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
    }

    $service = new UserService();
    $service->createUser($valid->getValidData());

    return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
  }
}