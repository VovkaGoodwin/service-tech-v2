<?php

namespace Core\Network\Switches\Errors;

class UnknownSwitchModelException extends \Exception {
    public function __construct(
      $message = "Неизвестная модель коммутатора."
    ) {
      parent::__construct($message);
    }
}