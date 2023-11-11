<?php

namespace Core\Network\Switches\Dlink;

class Des3028 extends DlinkSwitch {
  protected const PORT_STATE_OID = '1.3.6.1.4.1.171.11.63.6.2.2.2.1.3'; // .port.100
  protected const CLEAR_CRC_OID = '1.3.6.1.4.1.171.11.63.6.2.1.2.12.0'; // i 2
}