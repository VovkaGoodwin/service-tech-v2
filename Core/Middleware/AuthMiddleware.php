<?php

namespace Core\Middleware;

use App\Repositories\UserRepository;
use App\Services\AuthService;
use Core\Interfaces\ContainerInterface;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Cookies;
use Slim\Psr7\Factory\ResponseFactory;
use UnexpectedValueException;

class AuthMiddleware implements \Psr\Http\Server\MiddlewareInterface {

  private $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  /**
   * @inheritDoc
   */
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $authHeader = $request->getHeaderLine('Authorization');
    sscanf($authHeader, 'Bearer %s', $token);
    try {
      $paylodad = JWT::decode($token, JWT_KEY, [ 'HS256' ]);
    } catch (ExpiredException $e) {
      [,$bodyb64] = explode('.', $token);
      $paylodad = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));
      $needUpdateToken = $paylodad;

    } catch (UnexpectedValueException $e) {
      $paylodad = NULL;
    }

    if ($paylodad === null) {
      $responseFactory = new ResponseFactory();
      return $responseFactory->createResponse(401);
    }

    $userRepository = new UserRepository();
    $user = $userRepository->getUserById($paylodad->data->userId);
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
      $response = $response->withHeader('Set-Cookie', $cookie->toHeaders());
    }

    return $response;
  }
}