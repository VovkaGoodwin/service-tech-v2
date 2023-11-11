<?php

namespace Core\Traits;

use App\Entities\UserEntity;
use Core\Interfaces\ContainerInterface;
use Rakit\Validation\Validator;

/**
 * @property UserEntity currentUser
 * @property Validator validator
*/
trait ContainerTrait {
  private ContainerInterface $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  public function __get($name) {
    $property = $this->checkRegisteredClasses($name);
    if ($this->container->has($property)) {
      return $this->container->get($property);
    }
    return $this->{$property} ?? null;
  }

  private function checkRegisteredClasses($name) {
    switch ($name) {
      case 'validator':
        $name = Validator::class;
        break;
    }
    return $name;
  }
}