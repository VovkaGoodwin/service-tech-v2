<?php

namespace Core;

use Core\Rules\BoolRule;
use Dotenv\Dotenv;
use MessagePack\BufferUnpacker;
use RedBeanPHP\R;
use Rakit\Validation\Validator;
use Slim\Factory\AppFactory;
use const ROUTES;
final class App {
  private \Slim\App $app;

  public function __construct() {
    $container = Container::getInstance();
    $this->initContainer($container);

    $app = AppFactory::create(
      null,
      $container,
    );

    (require_once ROUTES . "/api.php")($app);
    (require_once ROUTES . "/web.php")($app);

    $this->app = $app;

    $this->initEnv();
    $this->initMiddlewares();
    $this->initDb();
  }

  private function initContainer(Container $container) {
    $validator = new Validator();
    $validator->addValidator('boolean', new BoolRule());

    $container->set(\Rakit\Validation\Validator::class, $validator);
  }

  private function initDb() {
    if (env('MODE') === 'prod') {
      R::setup(env('DSN', ''), env('DB_USER', ''), env('DB_PASSWORD', ''));
    }
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
    $this->app->addErrorMiddleware(getenv('MODE', 'dev') === 'dev', true, true);
  }

  public function run() {
    $this->app->run();
  }

}