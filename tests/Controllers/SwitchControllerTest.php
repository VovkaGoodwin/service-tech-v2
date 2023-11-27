<?php

namespace Controllers;

use App\Controllers\SwitchController;
use App\Controllers\UserController;
use App\Repositories\MockSwitchRepository;
use App\Services\SwitchService;
use App\Services\UserService;
use Core\Container;
use Faker\Factory;
use Fig\Http\Message\StatusCodeInterface;
use MessagePack\Packer;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockClass;
use PHPUnit\Framework\TestCase;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Psr7\Factory\ResponseFactory;

class SwitchControllerTest extends TestCase {

  private $mockService;
  private $request;
  private $response;
  private $controller;

  protected function setUp(): void {
    $this->mockService = \Mockery::mock(SwitchService::class);


    $container = Container::getInstance();
    $container->register([
      \App\Services\SwitchService::class => $this->mockService,
    ]);
    $this->request = (ServerRequestCreatorFactory::create())->createServerRequestFromGlobals();
    $this->response = (new ResponseFactory())->createResponse();
    $this->controller = new SwitchController($container);
  }

  public static function findSwitchDataProvider() {
    $repo = new MockSwitchRepository();
    $faker = Factory::create();

    $ip = $faker->ipv4();
    $portNumber = $faker->numberBetween(1, 24);
    $switch = $repo->findSwitch($ip);
    $port = $repo->findPort($ip, $portNumber);
    return [
      [
        'inputData' => [
          'ip' => $ip,
        ],
        'mockBehavior' => function (MockInterface $mock) use ($switch, $ip) {
          $mock->shouldReceive('findSwitch')
            ->with($ip)
            ->andReturn($switch);
        },
        'expectedResult' => (new Packer())->pack($switch),
        'expectedStatusCode' => StatusCodeInterface::STATUS_OK
      ], [
        'inputData' => [
          'ip' => $ip,
          'port' => $portNumber,
        ],
        'mockBehavior' => function (MockInterface $mock) use ($port, $ip, $portNumber) {
          $mock->shouldReceive('findPort')
            ->with($ip, $portNumber)
            ->andReturn($port);
        },
        'expectedResult' => (new Packer())->pack($port),
        'expectedStatusCode' => StatusCodeInterface::STATUS_OK
      ], [
        'inputData' => [
          'port' => $portNumber,
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('findPort', 'findSwitch');
        },
        'expectedResult' => '',
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ], [
        'inputData' => [
          'ip' => $faker->word(),
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('findPort', 'findSwitch');
        },
        'expectedResult' => '',
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ], [
        'inputData' => [
          'ip' => $ip,
          'port' => 1234,
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('findPort', 'findSwitch');
        },
        'expectedResult' => '',
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ]
    ];
  }

  #[DataProvider('findSwitchDataProvider')]
  public function testFindSwitch($inputData, $mockBehavior, $expectedResult, $expectedStatusCode) {
    $mockBehavior($this->mockService);

    $response = $this->controller->findSwitch($this->request->withQueryParams($inputData), $this->response);

    $this->assertSame($expectedResult, (string) $response->getBody());
    $this->assertSame($expectedStatusCode, $response->getStatusCode());
  }

  public static function clearCountersDataProvider() {
    $faker = Factory::create();

    $ip = $faker->ipv4();
    $port = $faker->numberBetween(1, 24);

    return [
      [
        'inputData' => [
          'ip' => $ip,
          'port' => $port,
        ],
        'mockBehavior' => function (MockInterface $mock) use ($ip, $port) {
          $mock->shouldReceive('clearCounters')
            ->with($ip, $port)
            ->andReturnTrue();
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_NO_CONTENT
      ], [
        'inputData' => [
          'ip' => $ip,
          'port' => $port,
        ],
        'mockBehavior' => function (MockInterface $mock) use ($ip, $port) {
          $mock->shouldReceive('clearCounters')
            ->with($ip, $port)
            ->andReturnFalse();
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
      ], [
        'inputData' => [
          'ip' => $ip,
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('clearCounters');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ], [
        'inputData' => [
          'port' => $port,
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('clearCounters');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ], [
        'inputData' => [
          'ip' => $ip,
          'port' => 28,
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('clearCounters');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ], [
        'inputData' => [
          'ip' => $ip,
          'port' => 'dasdadas',
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('clearCounters');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ], [
        'inputData' => [
          'ip' => ' asdasd ',
          'port' => $port,
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('clearCounters');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ]
    ];
  }

  #[DataProvider('clearCountersDataProvider')]
  public function testClearCounters($inputData, $mockBehavior, $expectedStatusCode) {
    $mockBehavior($this->mockService);

    $response = $this->controller->clearCounters($this->request->withQueryParams($inputData), $this->response);

    $this->assertSame($expectedStatusCode, $response->getStatusCode());
  }

  public static function rebootPortDataProvider() {
    $faker = Factory::create();

    $ip = $faker->ipv4();
    $port = $faker->numberBetween(1, 24);

    return [
      [
        'inputData' => [
          'ip' => $ip,
          'port' => $port,
        ],
        'mockBehavior' => function (MockInterface $mock) use ($ip, $port) {
          $mock->shouldReceive('rebootPort')
            ->with($ip, $port)
            ->andReturnTrue();
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_NO_CONTENT
      ], [
        'inputData' => [
          'ip' => $ip,
          'port' => $port,
        ],
        'mockBehavior' => function (MockInterface $mock) use ($ip, $port) {
          $mock->shouldReceive('rebootPort')
            ->with($ip, $port)
            ->andReturnFalse();
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
      ], [
        'inputData' => [
          'ip' => $ip,
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('rebootPort');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ], [
        'inputData' => [
          'port' => $port,
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('rebootPort');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ], [
        'inputData' => [
          'ip' => $ip,
          'port' => 28,
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('rebootPort');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ], [
        'inputData' => [
          'ip' => $ip,
          'port' => 'dasdadas',
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('rebootPort');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ], [
        'inputData' => [
          'ip' => ' asdasd ',
          'port' => $port,
        ],
        'mockBehavior' => function (MockInterface $mock) {
          $mock->shouldNotReceive('rebootPort');
        },
        'expectedStatusCode' => StatusCodeInterface::STATUS_BAD_REQUEST
      ]
    ];
  }

  #[DataProvider('rebootPortDataProvider')]
  public function testRebootPort($inputData, $mockBehavior, $expectedStatusCode) {
    $mockBehavior($this->mockService);

    $response = $this->controller->rebootPort($this->request->withQueryParams($inputData), $this->response);

    $this->assertSame($expectedStatusCode, $response->getStatusCode());
  }
}
