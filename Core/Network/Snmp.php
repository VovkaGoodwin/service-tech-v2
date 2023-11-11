<?php

namespace Core\Network;

class Snmp {
  public static function getModel($ip) {
    $modelString = snmp2_get($ip, READ_COMM, '1.3.6.1.2.1.1.1.0');
    if ($modelString) {
      preg_match('/(SNR-S2985G-24T)|(SNR-S2985G-24T-UPS)|(D.S.[0-9]{4})/', $modelString, $sw);
      return $sw[ 0 ];
    }
    return null;
  }

  public function get($ip, $object_id, $community = READ_COMM) {
    return snmp2_get($ip, $community, $object_id);
  }

  public function set($ip, $object_id, $type, $value, $community = SET_COMM) {
    error_log("snmp2_set('$ip', '$community', '$object_id', '$type', '$value')");
    snmp2_set($ip, $community, $object_id, $type, $value);
  }

  public function realWalk ($ip, $object_id, $community = READ_COMM) {
    return snmp2_real_walk($ip, $community, $object_id);
  }
}
