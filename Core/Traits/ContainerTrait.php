<?php

namespace Core\Traits;

use App\Entities\UserEntity;
use Core\Interfaces\ContainerInterface;

/**
 * @property UserEntity currentUser
*/
trait ContainerTrait {
  private ContainerInterface $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  public function __get($name) {
    if ($name === 'currentUser') {
      return $this->container->get($name);
    }
    return $this->{$name};
  }
}