<?php

namespace Core\Rules;

class BoolRule extends \Rakit\Validation\Rule {

  protected $message = "The :attribute must be a boolean";

  /**
   * Check the value is valid
   *
   * @param mixed $value
   * @return bool
   * @throws \Exception
   */
  public function check($value): bool
  {
    return \in_array($value, [\true, \false, "true", "false", "0", "1", "y", "n"]);
  }
}