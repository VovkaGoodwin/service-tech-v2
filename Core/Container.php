<?php

namespace Core;

use Core\Exceptions\ClassNotFoundException;

class Container implements \Core\Interfaces\ContainerInterface {

  private array $container = [];
  private array $registeredClasses = [];
  private array $madeInstances = [];
  private static $instance;

  private function __construct() {

  }

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

  public static function getInstance() {
    if (empty(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function register(array $classes) {
    foreach ($classes as $className => $classImplementation) {
      $this->registeredClasses[$className] = $classImplementation;
    }
  }

  public function make($classString, $new = false) {
    if (!array_key_exists($classString, $this->registeredClasses)) {
      throw new ClassNotFoundException();
    }
    
    $class = $this->registeredClasses[$classString];
    if (is_callable($class)) {
      $this->madeInstances[$classString] = $class();
    } else if (is_object($class)) {
      $this->madeInstances[$classString] = $class;
    } else {
      $this->madeInstances[$classString] = new $class;
    }

    return $this->madeInstances[$classString];
  }
}