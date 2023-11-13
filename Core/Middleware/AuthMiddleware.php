<?php

namespace Core\Middleware;

use App\Repositories\UserRepository;
use App\Services\AuthService;
use Core\Cache;
use Core\Interfaces\ContainerInterface;
use Core\Traits\ContainerTrait;
use Fig\Http\Message\StatusCodeInterface;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Cookies;
use Slim\Psr7\Factory\ResponseFactory;
use UnexpectedValueException;

class AuthMiddleware implements \Psr\Http\Server\MiddlewareInterface {

  use ContainerTrait;

  /**
   * @inheritDoc
   */
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $authHeader = $request->getCookieParams()['Authorization'];
    sscanf($authHeader, 'Bearer %s', $token);
    try {
      $paylodad = JWT::decode($token, env('TOKEN_KEY'), [ 'HS256' ]);
    } catch (ExpiredException $e) {
      [,$bodyb64] = explode('.', $token);
      $paylodad = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));
      $needUpdateToken = $paylodad;
    } catch (UnexpectedValueException $e) {
      $paylodad = NULL;
    }

    $cache = Cache::getInstance();

    if ($paylodad === null || $token != $cache->get($paylodad->data->userId)) {
      $responseFactory = new ResponseFactory();
      return $responseFactory->createResponse(StatusCodeInterface::STATUS_UNAUTHORIZED);
    }

    $user = (new UserRepository())->getUserById($paylodad->data->userId);
    $this->container->set('currentUser', $user);


    $response = $handler->handle($request);

    if (isset($needUpdateToken)) {
      $service = new AuthService();
      $newToken = $service->generateAccessToken($paylodad->data->userId);
      $cookie = new Cookies();
      $cookie->set('Authorization', [
        'value' => "Bearer {$newToken}",
        'expires' => time() + (int) env('TOKEN_TTL'),
        'path' => '/',
        'httponly' => true
      ]);
      $user->setToken($newToken);
      (new UserRepository())->updateUser($user);
      $response = $response->withHeader('Set-Cookie', $cookie->toHeaders());
    }

    return $response;
  }
}