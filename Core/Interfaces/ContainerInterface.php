<?php

namespace Core\Interfaces;

interface ContainerInterface extends \Psr\Container\ContainerInterface {
  public function set(string $key, $value);

  public function register(array $classes);

  public function make($classString);
}