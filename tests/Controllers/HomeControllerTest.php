<?php

namespace Controllers;

use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Repositories\MockHomeRepository;
use App\Services\HomeService;
use App\Services\UserService;
use Core\Container;
use Fig\Http\Message\StatusCodeInterface;
use MessagePack\Packer;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Psr7\Factory\ResponseFactory;

class HomeControllerTest extends TestCase {
  private $mockService;
  private $request;
  private $response;
  private $controller;
  protected function setUp(): void {
    $this->mockService = \Mockery::mock(HomeService::class);


    $container = Container::getInstance();
    $container->register([
      \App\Services\HomeService::class => $this->mockService,
    ]);
    $this->request = (ServerRequestCreatorFactory::create())->createServerRequestFromGlobals()->withMethod('POST');
    $this->response = (new ResponseFactory())->createResponse();
    $this->controller = new HomeController($container);
  }

  public static function findHomeDataProvider() {
    $repo = new MockHomeRepository();
    $fakeAbons = $repo->getHomeAbons(null, null);
    return [
      [
        'inputData' => [
          'street' => 'Мокрушина',
          'build' => '1'
        ],
        'mockBehavior' => function(MockInterface $mockService) use ($fakeAbons){
          $mockService->shouldReceive('getHomeAbons')
            ->with('Мокрушина', '1')
            ->andReturn($fakeAbons);
        },
        'expectedResponseBody' => (new Packer())->pack($fakeAbons),
        'expectedStatus' => StatusCodeInterface::STATUS_OK
      ], [
        'inputData' => [
          'street' => 'Мокрушина',
        ],
        'mockBehavior' => function(MockInterface $mockService) {
          $mockService->shouldNotReceive('getHomeAbons');
        },
        'expectedResponseBody' => '',
        'expectedStatus' => StatusCodeInterface::STATUS_BAD_REQUEST
      ], [
        'inputData' => [
          'build' => '1',
        ],
        'mockBehavior' => function(MockInterface $mockService) {
          $mockService->shouldNotReceive('getHomeAbons');
        },
        'expectedResponseBody' => '',
        'expectedStatus' => StatusCodeInterface::STATUS_BAD_REQUEST
      ]
    ];
  }

  #[DataProvider('findHomeDataProvider')]
  public function testFindHome($inputData, $mockBehavior, $expectedResponseBody, $expectedStatus) {
    $mockBehavior($this->mockService);

    $response = $this->controller->findHome($this->request->withQueryParams($inputData), $this->response);

    $this->assertSame($expectedStatus, $response->getStatusCode());
    $this->assertSame($expectedResponseBody, (string) $response->getBody());
  }
}
