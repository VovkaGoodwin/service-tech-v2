<?php

namespace Core;

class Container implements \Core\Interfaces\ContainerInterface {

  private array $container = [];

  /**
   * @inheritDoc
   */
  public function get(string $id) {
    if (array_key_exists($id, $this->container)) {
      return $this->container[$id];
    }
    return null;
  }

  /**
   * @inheritDoc
   */
  public function has(string $id): bool {
    return array_key_exists($id, $this->container);
  }

  public function set(string $key, $value) {
    $this->container[$key] = $value;
  }
}