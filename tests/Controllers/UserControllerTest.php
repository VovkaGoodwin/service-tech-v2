<?php

namespace Controllers;

use App\Controllers\UserController;
use App\Services\UserService;
use Core\Container;
use Core\Rules\BoolRule;
use Fig\Http\Message\StatusCodeInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rakit\Validation\Validator;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Psr7\Factory\ResponseFactory;

class UserControllerTest extends TestCase {

  private $mockService;
  private $request;
  private $response;
  private $controller;

  protected function setUp(): void {
    $this->mockService = \Mockery::mock(UserService::class);


    $container = Container::getInstance();
    $container->register([
      \App\Services\UserService::class => $this->mockService,
    ]);
    $this->request = (ServerRequestCreatorFactory::create())->createServerRequestFromGlobals()->withMethod('POST');
    $this->response = (new ResponseFactory())->createResponse();
    $this->controller = new UserController($container);
  }
  public static function createUserDateProvider() {
    return [
      [
        'inputData' => [
          'login' => 'kolya',
          'password' => '123456',
          'firstName' => 'Kolya',
          'lastName' => 'Uskov',
          'phone' => '88005553535',
          'isAdmin' => true
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldReceive('createUser')->with(['login' => 'kolya', 'password' => '123456', 'firstName' => 'Kolya', 'lastName' => 'Uskov', 'phone' => '88005553535', 'isAdmin' => true]);
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_CREATED
      ], [
        'inputData' => [
          'login' => 'kolya',
          'password' => '123456',
          'firstName' => 'Kolya',
          'lastName' => 'Uskov',
          'phone' => '88005553535',
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldReceive('createUser')->with(['login' => 'kolya', 'password' => '123456', 'firstName' => 'Kolya', 'lastName' => 'Uskov', 'phone' => '88005553535', 'isAdmin' => null]);
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_CREATED
      ], [
        'inputData' => [
          'login' => 'kolya',
          'password' => '123456',
          'firstName' => 'Kolya',
          'phone' => '88005553535',
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('createUser');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ],
    ];
  }

  #[DataProvider('createUserDateProvider')]
  public function testCreateUser($inputData, $mockBehavior, $expectedStatusCode) {
    $mockBehavior($this->mockService);

    $response = $this->controller->createUser($this->request->withParsedBody($inputData), $this->response);

    $this->assertSame($expectedStatusCode, $response->getStatusCode());
  }

  public static function deleteUserDataProvider() {
    return [
      [
        'inputData' => 1,
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldReceive('deleteUser')->with(1);
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_NO_CONTENT
      ]
    ];
  }

  #[DataProvider('deleteUserDataProvider')]
  public function testDeleteUser($inputData, $mockBehavior, $expectedStatusCode) {
    $mockBehavior($this->mockService);

    $response = $this->controller->deleteUser($this->request, $this->response, [ 'id' => $inputData ]);

    $this->assertSame($expectedStatusCode, $response->getStatusCode());
  }
}
