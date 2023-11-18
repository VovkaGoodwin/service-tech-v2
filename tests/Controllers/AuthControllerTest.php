<?php

namespace Controllers;

use App\Controllers\AuthController;
use App\Entities\UserEntity;
use Core\Container;
use Fig\Http\Message\StatusCodeInterface;
use MessagePack\Packer;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Psr7\Cookies;
use Slim\Psr7\Factory\ResponseFactory;

class AuthControllerTest extends TestCase {

  private $mockService;
  private $request;
  private $response;
  private $controller;


  protected function setUp(): void {
    $this->mockService = \Mockery::mock(\App\Services\AuthService::class);
    $container = Container::getInstance();
    $container->register([
      \App\Services\AuthService::class => $this->mockService,
    ]);
    $this->request = (ServerRequestCreatorFactory::create())->createServerRequestFromGlobals()->withMethod('POST');
    $this->response = (new ResponseFactory())->createResponse();
    $this->controller = new AuthController($container);
  }

  public function loginDataProvider (): array {
    $userMock = \Mockery::mock(UserEntity::class);
    $userMock->shouldReceive('getSafetyData')->andReturn([
      'id' => 1,
      'login' => 'vovka',
      'firstName' => 'vovka',
      'lastName' => 'goodwin',
      'phone' => '88005553535',
      'isAdmin' => true,
    ]);
    $userMock->shouldReceive('getToken')->andReturn('authorization_token');

    return [
      [
        'inputData' => ['login' => 'vovka', 'password' => '123456'],
        'mockBehavior' => function (MockInterface $mockService) use ($userMock) {
          $mockService->shouldReceive('login')->with('vovka', '123456')->andReturn($userMock);
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_OK,
        'expectedCookie' => (new Cookies())->set('Authorization', [
          'value' => 'Bearer authorization_token',
          'expires' => time() + 28800,
          'path' => '/',
          'httponly' => true
        ]),
        'expectedResponseBody' => (new Packer())->pack(['user' => $userMock->getSafetyData()]),
      ], [
        'inputData' => ['login' => 'vovka'],
        'mockBehavior' => function (MockInterface $mockService) use ($userMock) {
          $mockService->shouldNotReceive('login');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST,
        'expectedCookie' => new Cookies(),
        'expectedResponseBody' => '',
      ], [
        'inputData' => ['password' => '123456'],
        'mockBehavior' => function (MockInterface $mockService) use ($userMock) {
          $mockService->shouldNotReceive('login');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST,
        'expectedCookie' => new Cookies(),
        'expectedResponseBody' => '',
      ], [
        'inputData' => ['login' => 'vovka', 'password' => '123456'],
        'mockBehavior' => function (MockInterface $mockService) use ($userMock) {
          $mockService->shouldReceive('login')->with('vovka', '123456')->andReturn(null);
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST,
        'expectedCookie' => new Cookies(),
        'expectedResponseBody' => '',
      ]
    ];
  }

  #[DataProvider('loginDataProvider')]
  public function testLogin($inputData, $mockBehavior, $expectedStatusCode, $expectedCookie, $expectedResponseBody) {
    $mockBehavior($this->mockService);

    $response = $this->controller->logIn($this->request->withParsedBody($inputData), $this->response);

    $this->assertSame($expectedStatusCode, $response->getStatusCode());
    $this->assertSame($expectedCookie->toHeaders(), $response->getHeader('Set-Cookie'));
    $this->assertSame($expectedResponseBody, (string) $response->getBody());
  }

  public function testLogOut() {
    $response = $this->controller->logOut($this->request, $this->response);
    $cookie = new Cookies();
    $cookie->set('Authorization', [
      'value' => '',
      'expires' => time() - 1,
    ]);
    $this->assertSame($cookie->toHeaders(), $response->getHeader('Set-Cookie'));
  }
}
