<?php

namespace Core\Network;

interface ISwitch {
  public const MODEL_OID = '1.3.6.1.2.1.1.1.0';

  public const INTEGER_TYPE = 'i';
  public const PORT_STATE_DISABLED = '2';
  public const PORT_STATE_ENABLED = '1';

  public function restartPort($port);
  public function clearCrcCounter($port);

  public function getFullInfoAboutPort($port);

  public function getFullInfoAboutAllPorts();

  public function getCrcCount($port);

  public function getL2Data($port);

  public function getDescription($port);

  public function getPortStatus($port);

  public function getPairStatus($port, $pairNum);

  public function getPairLength($port, $pairNum);

  public function getSpeed($port);

  public function startPortDiagnostic();
}