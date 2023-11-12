<?php

namespace App\Repositories;

use Faker\Factory;

class MockHomeRepository {
  public function getHomeAbons($street, $build) {
    $faker = Factory::create();
    $faker->unique();
    $data = [];

    for ($i = 0; $i < 33; $i++) {

      $data[] = [
        'ls' => $faker->numberBetween(22000, 33000),
        'login' => $faker->userName(),
        'flat' => $faker->numberBetween(1, 120),
        'tariff' => $faker->word(),
        'balance' => $faker->randomFloat(),
        'start_block' => $faker->date('d/m/y'),
        'end_block' => $faker->date('d/m/y'),
        'switch' => $faker->ipv4(),
        'port' => $faker->numberBetween(1, 24),
        'ip' => $faker->localIpv4()
      ];
    }
    return $data;
  }
}