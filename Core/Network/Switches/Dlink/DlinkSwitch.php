<?php

namespace Core\Network\Switches\Dlink;

class DlinkSwitch extends \Core\Network\Switches\BaseSwitch {
  protected const PORT_COUNT = 24;
  protected const PORT_STATE_OID = '1.3.6.1.4.1.171.12.58.1.1.1.3';
  protected const PORT_STATUS_OID = '1.3.6.1.4.1.171.12.58.1.1.1.3'; // .port
  protected const PAIR_STATUS_OID = '1.3.6.1.4.1.171.12.58.1.1.1'; //.pairId.port
  protected const PAIR_LENGTH_OID = '1.3.6.1.4.1.171.12.58.1.1.1'; // .pairId.port
  protected const TEST_PORT_OID = '1.3.6.1.4.1.171.12.58.1.1.1.12'; // .port type = 'i' value = 1


  public function startPortDiagnostic() {
    for ($i = 1; $i < static::PORT_COUNT; $i ++) {
      $this->testPort($i);
    }
    sleep(1);
  }

  private function testPort($port){
    $this->snmp->set($this->ip, static::TEST_PORT_OID. ".{$port}", 'i', '1');
  }
}