<?php

namespace Core\Network;

interface ISwitchFactory {
  public function create($ip);
}