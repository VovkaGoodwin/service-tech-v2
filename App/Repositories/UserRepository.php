<?php

namespace App\Repositories;

use App\Entities\UserEntity;
use Core\Exceptions\UserNotFoundException;

class UserRepository {
  private array $users;
  private int $lastId;

  public function __construct() {
    $data = unserialize(file_get_contents(ROOT . '/users.txt'));
    $this->users = $data['users'];
    $this->lastId = $data['lastId'];
  }

  public function __destruct() {
    $data = [
      'users' => $this->users,
      'lastId' => $this->lastId,
    ];
    file_put_contents(ROOT . '/users.txt', serialize($data));
  }

  public function isUserExists($login, $firstName, $lastName) {
    foreach ($this->users as $user) {
      if ($user['login'] === $login && $user['firstName'] === $firstName && $user['lastName'] === $lastName) {
        return true;
      }
    }
    return false;
  }

  /**
   * @throws UserNotFoundException
   */
  public function getUserByCredentials($login, $password) {
    foreach ($this->users as $user) {
      if ($user['login'] === $login && $user['password'] === $password) {
        return $this->getUserById($user['id']);
      }
    }
    $this->userNotFound();
  }

  /**
   * @throws UserNotFoundException
   */
  public function getUserById($id) {
    $userData = null;
    foreach ($this->users as $user) {
      if ($user['id'] == $id) {
        $userData = $user;
        break;
      }
    }

    if ($userData !== null) {
      return new UserEntity(
        $userData['id'],
        $userData['login'],
        $userData['password'],
        $userData['firstName'],
        $userData['lastName'],
        $userData['phone'],
        $userData['token'],
        $userData['isAdmin'],
      );
    }
    $this->userNotFound();
  }

  public function updateUser(UserEntity $user) {
    foreach ($this->users as $idx => $savedUser) {
      if ($savedUser['id'] == $user->id) {
        $this->users[$idx] = $user->getDataArray();
        return true;
      }
    }
    return false;
  }

  public function createUser(UserEntity $user) {
    $newId = ++$this->lastId;
    $user->id = $newId;
    $this->users[] = (array) $user;
    return $newId;
  }

  public function delete($id) {
    $this->users = array_filter($this->users, function ($user) use ($id) {
      return $user['id'] != $id;
    });
  }

  public function userNotFound() {
    throw new UserNotFoundException();
  }
}