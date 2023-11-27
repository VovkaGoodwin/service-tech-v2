<?php

namespace App\Controllers;

use App\Services\SwitchService;
use Core\BaseController;
use Core\Traits\ContainerTrait;
use Core\Traits\MsgpackTrait;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SwitchController extends BaseController {

  public function findSwitch(ServerRequestInterface $request, ResponseInterface $response) {
    $queryParams = $request->getQueryParams();
    $valid = $this->validator->validate($queryParams, [
      'ip' => 'required|ipv4',
      'port' => 'integer|max:24'
    ]);

    if ($valid->fails()) {
      return $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
    }

    $service = $this->container->make(SwitchService::class);
    if ($valid->getValue('port')) {
      $data = $service->findPort($valid->getValue('ip'), $valid->getValue('port'));
    } else {
      $data = $service->findSwitch($valid->getValue('ip'));
    }

    $body = $response->getBody();
    $body->write($this->encode($data));

    return $response->withStatus(StatusCodeInterface::STATUS_OK)->withBody($body);
  }

  public function rebootPort(ServerRequestInterface $request, ResponseInterface $response) {
    $valid = $this->validator->validate($request->getQueryParams(), [
      'ip' => 'required|ipv4',
      'port' => 'required|integer|max:25'
    ]);

    if ($valid->fails()) {
      return $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
    }

    $service = $this->container->make(SwitchService::class);
    $result = $service->rebootPort($valid->getValue('ip'), $valid->getValue('port'));

    return $response->withStatus($result ? StatusCodeInterface::STATUS_NO_CONTENT : StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
  }

  public function clearCounters(ServerRequestInterface $request, ResponseInterface $response) {
    $valid = $this->validator->validate($request->getQueryParams(), [
      'ip' => 'required|ipv4',
      'port' => 'required|integer|max:25'
    ]);

    if ($valid->fails()) {
      return $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
    }

    $service = $this->container->make(SwitchService::class);
    $result = $service->clearCounters($valid->getValue('ip'), $valid->getValue('port'));

    return $response->withStatus($result ? StatusCodeInterface::STATUS_NO_CONTENT : StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
  }
}