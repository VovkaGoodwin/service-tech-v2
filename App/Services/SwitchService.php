<?php

namespace App\Services;

use App\Repositories\MockSwitchRepository;
use App\Repositories\SwitchRepository;

class SwitchService {

  private $repository;
  public function __construct() {
    $this->repository = env('MODE', 'dev') === 'prod' ? new SwitchRepository() : new MockSwitchRepository();
  }

  public function findSwitch($ip) {
    return $this->repository->findSwitch($ip);
  }

  public function findPort($ip, $port) {
    return $this->repository->findPort($ip, $port);
  }

  public function rebootPort($ip, $port) {
    return $this->repository->rebootPort($ip, $port);
  }

  public function clearCounters($ip, $port) {
    return $this->repository->clearCounters($ip, $port);
  }
}