<?php

namespace App\Services;

use App\Entities\UserEntity;
use App\Repositories\UserRepository;

class UserService {
  public function createUser($data) {
    $user = new UserEntity(
      0,
      $data['login'],
      $data['password'],
      $data['firstName'],
      $data['lastName'],
      $data['phone'],
      '',
      $data['isAdmin']
    );
    $repository = new UserRepository();
    if ($repository->isUserExists($user->login, $user->firstName, $user->lastName)) {
      return false;
    }

    return (bool) $repository->createUser($user);
  }
}