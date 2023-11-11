<?php

namespace App\Repositories;

use core\network\Switches\Errors\UnknownSwitchModelException;
use Core\Network\SwitchFactory;

class SwitchRepository {

  private SwitchFactory $factory;

  public function __construct() {
    $this->factory = new SwitchFactory();
  }

  public function findSwitch($ip) {
    try {
      $switch = $this->factory->create($ip);
      return $switch->getFullInfoAboutAllPorts();
    } catch (UnknownSwitchModelException $e) {
      return null;
    }
  }

  public function findPort($ip, $port) {
    try {
      $switch = $this->factory->create($ip);
      return $switch->getFullInfoAboutPort($port);
    } catch (UnknownSwitchModelException $e) {
      return null;
    }
  }

  public function rebootPort($ip, $port) {
    try {
      $switch = $this->factory->create($ip);
      $switch->restartPort($port);
      return true;
    } catch (UnknownSwitchModelException $e) {
      return false;
    }
  }

  public function clearCounters($ip, $port) {
    try {
      $switch = $this->factory->create($ip);
      $switch->clearCrcCounter($port);
      return true;
    } catch (UnknownSwitchModelException $e) {
      return false;
    }
  }
}