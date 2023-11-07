<?php

namespace App\Entities;

class UserEntity {
  public $login;
  public $password;
  public $firstName;
  public $lastName;
  public $phone;
  public $isAdmin;
  public $token = '';
  public $id;

  public function __construct(
    $id,
    $login,
    $password,
    $firstName,
    $lastName,
    $phone,
    $token,
    $isAdmin = false
  ) {
    $this->id = $id;
    $this->login = $login;
    $this->password = $password;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->phone = $phone;
    $this->token = $token;
    $this->isAdmin = $isAdmin;
  }

  public function getDataArray() {
    return (array) $this;
  }

  /**
   * @param string $token
   */
  public function setToken(string $token) {
    $this->token = $token;
  }
}