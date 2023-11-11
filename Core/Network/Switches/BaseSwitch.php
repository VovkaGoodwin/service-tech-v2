<?php

namespace Core\Network\Switches;

use Core\Network\Snmp;

abstract class BaseSwitch implements \Core\Network\ISwitch {

  protected const PORT_COUNT = null;
  protected const PORT_STATE_OID = null;
  protected const CLEAR_CRC_OID = null;
  protected const CRC_COUNT_OID = '1.3.6.1.2.1.16.1.1.1.8'; // . port
  protected const DESCRIPTION_OID = '1.3.6.1.2.1.31.1.1.1.18'; // . port
  protected const PORT_STATUS_OID = null;
  protected const PAIR_STATUS_OID = null;
  protected const PAIR_LENGTH_OID = null;
  protected const PORT_SPEED_OID = '1.3.6.1.2.1.2.2.1.5'; // port
  protected const TEST_PORT_OID = null;
  protected const L2_DATA_OID = '1.3.6.1.2.1.17.7.1.2.2.1.2';

  protected $snmp = null;
  protected $ip = null;

  public function __construct($ip) {
    $this->ip = $ip;
    $this->snmp = new Snmp();
  }

  protected function disablePort($port) {
    $this->snmp->set(
      $this->ip,
      static::PORT_STATE_OID . ".{$port}.100",
      'i',
      '2'
    );
  }

  protected function enablePort($port) {
    $this->snmp->set(
      $this->ip,
      static::PORT_STATE_OID . ".{$port}.100",
      'i',
      '3'
    );
  }

  public function restartPort($port) {
    $this->disablePort($port);
    sleep(1);
    $this->enablePort($port);
  }

  public function clearCrcCounter($port) {
    $this->snmp->set(
      $this->ip,
      static::CLEAR_CRC_OID,
      'i',
      '3'
    );
  }

  public function getFullInfoAboutPort($port) {
    return [
      'status' => $this->getPortStatus($port),
      'pair1Status' => $this->getPairStatus($port, 1),
      'pair1Length' => $this->getPairLength($port, 1),
      'pair2Status' => $this->getPairStatus($port, 2),
      'pair2Length' => $this->getPairLength($port, 2),
      'description' => $this->getDescription($port),
      'crcCount' => $this->getCrcCount($port),
      'l2Data' => $this->getL2Data($port),
      'ip' => $this->ip,
      'speed' => $this->getSpeed($port)
    ];
  }

  public function getFullInfoAboutAllPorts() {
    $data = [];
    for ($port = 1; $port <= static::PORT_COUNT; $port++) {
      $data[$port] = $this->getFullInfoAboutPort($port);
    }
    return $data;
  }

  public function getCrcCount($port) {
    $count = $this->snmp->get($this->ip, static::CRC_COUNT_OID . ".{$port}");
    return preg_replace('/^.*:\s?\D*/', '', $count);
  }

  public function getL2Data($port) {
    $response = $this->snmp->realWalk($this->ip, static::L2_DATA_OID);
    $data = [];
    foreach ($response as $index => $item) {
      $item = str_replace('INTEGER: ', '', $item);
      if ($item != $port) continue;
      $index = str_replace('iso.3.6.1.2.1.17.7.1.2.2.1.2.', '', $index);
      $octets = preg_split('/\./', $index);
      $vlan = $octets[0];
      unset($octets[0]);
      foreach ($octets as &$octet) {
        $octet = dechex($octet);
        $octet = (strlen($octet) == 1) ? '0'.$octet : $octet;
      }
      $mac = join(':', $octets);
      $data[$vlan][] = $mac;
      break;
    }
    return $data;
  }

  public function getDescription($port) {
    $description = $this->snmp->get($this->ip, static::DESCRIPTION_OID . ".{$port}");
    return str_ireplace('STRING: ', '', str_ireplace('"', '', $description));
  }

  public function getPortStatus($port) {
    $status = $this->snmp->get($this->ip,static::PORT_STATUS_OID . ".{$port}");
    $status = str_ireplace('INTEGER: ', '', $status);
    return ($status == 1) ? 'Link-Up' : 'Link-Down';
  }

  public function getPairStatus($port, $pairNum) {
    switch ($pairNum) {
      case 1:
        $pairOid = 4;
        break;
      case 2:
        $pairOid = 5;
        break;
    }
    $pairStatus = $this->snmp->get($this->ip,static::PAIR_STATUS_OID . ".{$pairOid}.{$port}");
    $pairStatus = str_ireplace('INTEGER: ', '', $pairStatus);
    switch ($pairStatus){
      case '0':
        return 'Ok';
      case '1':
        return 'Open';
      case '2':
        return 'Short';
      case '3':
        return 'Open-short';
      case '4':
        return 'Crosstalk';
      case '5':
        return 'Unknown';
      case '6':
        return 'Count';
      case '7':
        return 'No Cable';
      default:
        return 'Other';
    }
  }

  public function getPairLength($port, $pairNum) {
    switch ($pairNum) {
      case 1:
        $pairOid = 8;
        break;
      case 2:
        $pairOid = 9;
        break;
    }
    $pairLength = $this->snmp->get($this->ip, static::PAIR_LENGTH_OID.".{$pairOid}.{$port}");
    $pairLength = str_ireplace('INTEGER: ', '', $pairLength);
    return (integer) $pairLength;
  }

  public function getSpeed($port) {
    $speed = $this->snmp->get($this->ip, static::PORT_SPEED_OID . ".{$port}");
    $speed = str_replace('Gauge32: ', '', $speed);
    return $speed / 1000000;
  }

  public abstract function startPortDiagnostic();
}