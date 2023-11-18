<?php

namespace Core\Exceptions;

class ClassNotFoundException extends \Exception {
  public function __construct() {
    parent::__construct("Класс не найден");
  }
}