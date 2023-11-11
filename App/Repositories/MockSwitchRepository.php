<?php

namespace App\Repositories;

class MockSwitchRepository {
  public function findSwitch($ip) {
    $data = [];
    for ($i = 0; $i < 24; $i++) {
      $data[] = [
        'status' => 'UP',
        'pair1Status' => "OK",
        'pair1Length' => "22",
        'pair2Status' => "OK",
        'pair2Length' => "22",
        'description' => "",
        'crcCount' => "0",
        'l2Data' => [
          "vlan1" => "mac1"
        ],
        'ip' => $ip,
        'speed' => "100"
      ];
    }

    return $data;
  }

  public function findPort($ip, $port) {
    return [
      'status' => 'UP',
      'pair1Status' => "OK",
      'pair1Length' => "22",
      'pair2Status' => "OK",
      'pair2Length' => "22",
      'description' => "",
      'crcCount' => "0",
      'l2Data' => [
        "vlan1" => "mac1"
      ],
      'ip' => $ip,
      'speed' => "100"
    ];
  }
  public function rebootPort($ip, $port) {
    return true;
  }

  public function clearCounters($ip, $port) {
    return true;
  }
}