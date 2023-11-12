<?php

namespace App\Repositories;

use RedBeanPHP\R;

class HomeRepository {
  public function getHomeAbons($street, $build) {
    $searchString = "%{$street}%, {$build}, кв.%";
    $homeAbons = R::getAssoc("SELECT id, login, basic_account ls, SUBSTRING_INDEX(users.actual_address,'.',-1) as flat 
                                               FROM users WHERE actual_address like ?
                                               ORDER BY (flat+0)", [ $searchString ]);

    return array_map(function ($id, $data) {
      $tariff_data = R::getAll("SELECT sl.user_id,
                                              t.name, 
                                              round (a.balance+a.credit, 2) balance, 
                                              FROM_UNIXTIME(bi.start_date) start_block, 
                                              FROM_UNIXTIME(bi.expire_date) end_block
                                        FROM service_links sl
                                        LEFT JOIN services_data sd ON sd.id = sl.service_id
                                        LEFT JOIN tariffs t        ON t.id = sd.tariff_id
                                        LEFT JOIN accounts a       ON a.id = sl.account_id
                                        LEFT JOIN blocks_info bi   ON bi.id = a.block_id
                                        WHERE sl.user_id = {$id} AND sl.is_deleted = 0
                                        ORDER BY sl.id DESC LIMIT 1")[0];

      $switch = R::getAll("SELECT value FROM user_additional_params WHERE userid = {$id} AND paramid = 2 LIMIT 1")[0]['value'];
      $port = R::getAll("SELECT value FROM user_additional_params WHERE userid = {$id} AND paramid = 3 LIMIT 1")[0]['value'];
      $ip = R::getAll("SELECT value FROM user_additional_params WHERE userid = {$id} AND paramid = 4 LIMIT 1")[0]['value'];
      return [
        'ls' => $data['ls'],
        'login' => $data['login'],
        'flat' => $data['flat'],
        'tariff' => $tariff_data['name'],
        'balance' => $tariff_data['balance'],
        'start_block' => $tariff_data['start_block'],
        'end_block' => $tariff_data['end_block'],
        'switch' => $switch,
        'port' => $port,
        'ip' => $ip
      ];
    }, array_keys($homeAbons), $homeAbons);
  }
}