<?php

namespace Core\Network\Switches\Dlink;

class Des3526 extends DlinkSwitch {
  protected const PORT_STATE_OID = '1.3.6.1.4.1.171.11.64.1.2.4.2.1.3'; // .port.100
  protected const CLEAR_CRC_OID = '1.3.6.1.4.1.171.11.64.1.2.1.2.8.0'; // i 2
}