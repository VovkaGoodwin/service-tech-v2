<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Core\Cache;
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

    $token = $this->generateAccessToken($user->id);
    $user->setToken($token);
    $userRepository->updateUser($user);


    return $user;
  }

  public function generateAccessToken($userId) {
    $payload = [
      'iss' => 'tomsk.zelenaya.net',
      'aud' => 'tomsk.zelenaya.net',
      'iat' => time(),
      'nbf' => time(),
      'exp' => time() + (int) env('TOKEN_TTL'),
      'data' => [
        'userId' => $userId
      ]
    ];

    $token = JWT::encode($payload, env('TOKEN_KEY'));
    $cache = Cache::getInstance();
    $cache->set($userId, $token, (int) env('TOKEN_TTL') + 3600);

    return $token;
  }

  private function getPasswordHash(string $password) {
    return base64_encode($password);
  }

  private function generateRefreshToken($userId) {
    $payload = [
      'iss' => 'tomsk.zelenaya.net',
      'aud' => 'tomsk.zelenaya.net',
      'iat' => time(),
      'nbf' => time(),
      'exp' => time() + (int) env('REFRESH_TOKEN_TTL'),
      'data' => [
        'userId' => $userId
      ]
    ];

    return bin2hex(JWT::encode($payload, env('REFRESH_TOKEN_KEY')));
  }
}