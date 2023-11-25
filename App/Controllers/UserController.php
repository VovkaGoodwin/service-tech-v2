<?php

namespace App\Controllers;

use App\Services\UserService;
use Core\BaseController;
use Core\Traits\ContainerTrait;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rakit\Validation\Validator;

class UserController extends BaseController {

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

    $service = $this->container->make(UserService::class);
    $service->createUser($valid->getValidData());

    return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
  }

  public function deleteUser(ServerRequestInterface $request, ResponseInterface $response, $args) {
    ['id' => $userId] = $args;
    $service = new UserService();
    $service->deleteUser($userId);

    return $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
  }
}