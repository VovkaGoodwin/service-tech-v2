<?php

namespace Core\Exceptions;

use Exception;

class UserNotFoundException extends Exception {
  public function __construct() {
    parent::__construct('Пользователь не найден.', 0, null);
  }
}