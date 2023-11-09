<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Core\Exceptions\UserNotFoundException;
use Firebase\JWT\JWT;

class AuthService {
  public function login(string $login, string $password) {
    $userRepository = new UserRepository();
    try {
      $user = $userRepository->getUserByCredentials($login, $this->getPasswordHash($password));
    } catch (UserNotFoundException $e) {
      return null;
    }

    $payload = [
      'iss' => 'tomsk.zelenaya.net',
      'aud' => 'tomsk.zelenaya.net',
      'iat' => time(),
      'nbf' => time(),
      'exp' => time() + (int) env('TOKEN_TTL'),
      'data' => [
        'user' => $user->getSafetyData()
      ]
    ];

    $token = JWT::encode($payload, JWT_KEY);
    $user->setToken($token);
    $userRepository->updateUser($user);


    return $user;
  }

  private function getPasswordHash(string $password) {
    return base64_encode($password);
  }
}