<?php

namespace App\Controllers;

use App\Services\HomeService;
use Core\Traits\ContainerTrait;
use Core\Traits\MsgpackTrait;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController {
  use ContainerTrait, MsgpackTrait;
  public function findHome(ServerRequestInterface $request, ResponseInterface $response) {
    $valid = $this->validator->validate($request->getQueryParams(), [
      'street' => 'required|max:30',
      'build' => 'required|max:10',
    ]);

    if ($valid->fails()) {
      return $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
    }
    $service = new HomeService();
    $homeAbons = $service->getHomeAbons($valid->getValue('street'), $valid->getValue('build'));

    $body = $response->getBody();
    $body->write($this->encode($homeAbons));

    return $response->withStatus(StatusCodeInterface::STATUS_OK)->withBody($body);
  }
}