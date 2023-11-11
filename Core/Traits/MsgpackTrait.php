<?php

namespace Core\Traits;

use MessagePack\BufferUnpacker;
use MessagePack\Packer;

trait MsgpackTrait {
  public function encode($data) {
    $packer = new Packer();
    return $packer->pack($data);
  }

  public function decode($data) {
    $unpacker = new BufferUnpacker();

    $unpacker->reset($data);
    return $unpacker->unpack();
  }
}