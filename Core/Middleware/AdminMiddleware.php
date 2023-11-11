<?php

namespace Core\Middleware;

use Core\Interfaces\ContainerInterface;
use Core\Traits\ContainerTrait;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class AdminMiddleware implements MiddlewareInterface {
  use ContainerTrait;
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    if ($this->currentUser->isAdmin) {
      return $handler->handle($request);
    }
    $responseFactory = new ResponseFactory();
    return $responseFactory->createResponse(StatusCodeInterface::STATUS_FORBIDDEN);
  }
}