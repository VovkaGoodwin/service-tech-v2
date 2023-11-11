<?php

namespace Core;

use Dotenv\Dotenv;
use MessagePack\BufferUnpacker;
use Slim\Factory\AppFactory;
use const ROUTES;
final class App {
  private \Slim\App $app;

  public function __construct() {
    $container = new Container();

    $app = AppFactory::create(
      null,
      $container,
    );

    (require_once ROUTES . "/api.php")($app);
    (require_once ROUTES . "/web.php")($app);

    $this->app = $app;

    $this->initEnv();
    $this->initMiddlewares();
  }

  private function initEnv() {
    $dotenv = Dotenv::createImmutable(ROOT);
    $dotenv->load();
  }

  private function initMiddlewares() {
    $msgpackParser = static function ($input) {
      $unpacker = new BufferUnpacker();

      $unpacker->reset($input);
      return $unpacker->unpack();
    };

    $this->app->addRoutingMiddleware();
    $this->app->addBodyParsingMiddleware(['application/x-msgpack' => $msgpackParser]);
    $this->app->addErrorMiddleware(getenv('PRODUCTION'), true, true);
  }

  public function run() {
    $this->app->run();
  }

}