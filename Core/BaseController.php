<?php

namespace Core;

use Core\Rules\BoolRule;
use MessagePack\BufferUnpacker;
use MessagePack\Packer;
use Rakit\Validation\Validator;

class BaseController {

  protected $container;
  protected $packer;
  protected $unpacker;
  protected $validator;

  public function __construct(Container $container) {
    $this->container = $container;

    $this->packer = new Packer();
    $this->unpacker = new BufferUnpacker();

    $this->validator = new Validator();
    $this->validator->addValidator('boolean', new BoolRule());
  }

  public function encode($data) {
    return $this->packer->pack($data);
  }

  public function decode($data) {
    $this->unpacker->reset($data);
    return $this->unpacker->unpack();
  }


}