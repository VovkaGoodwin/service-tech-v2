<?php

namespace Core\Interfaces;

interface ContainerInterface extends \Psr\Container\ContainerInterface {
  public function set(string $key, $value);
}