<?php

namespace Core\Network;

use Core\Network\Switches\Dlink\Des3028;
use Core\Network\Switches\Dlink\Des3200;
use Core\Network\Switches\Dlink\Des3526;

use Core\Network\Switches\Snr\SnrS2985G24TUps;

use Core\Network\Switches\Errors\UnknownSwitchModelException;

class SwitchFactory implements ISwitchFactory {

  public function create($ip) {
    $model = Snmp::getModel($ip);

    switch ($model) {
      case "DES-3200":
        $switch = new Des3200($ip);
        break;
      case "DES-3526":
        $switch = new Des3526($ip);
        break;
      case "DES-3028":
        $switch = new Des3028($ip);
        break;
      case "SNR-S2985G-24T-UPS":
      case "SNR-S2985G-24T":
        $switch = new SnrS2985G24TUps($ip);
        break;
      default:
        throw new UnknownSwitchModelException();
    }
    return $switch;
  }

}
