<?php

namespace App\Controllers;

use App\Services\TicketsService;
use Core\Traits\ContainerTrait;
use Core\Traits\MsgpackTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TicketsController {
  use MsgpackTrait, ContainerTrait;
  public function getTickets(ServerRequestInterface $request, ResponseInterface $response) {
    $service = new TicketsService();

    $body = $response->getBody();
    $body->write($this->encode($service->getTickets()));

    return $response->withBody($body);
  }
}