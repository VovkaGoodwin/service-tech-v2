<?php

namespace App\Services;

use App\Repositories\HomeRepository;
use App\Repositories\MockHomeRepository;

class HomeService {
  public function getHomeAbons($street, $build) {
    $repository = env('mode') === 'production' ? new HomeRepository() : new MockHomeRepository();
    return $repository->getHomeAbons($street, $build);
  }
}