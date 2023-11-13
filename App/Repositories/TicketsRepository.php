<?php

namespace App\Repositories;

use App\Entities\UserEntity;
use Core\Container;
use Core\Traits\ContainerTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TicketsRepository {
  use ContainerTrait;

  private Client $httpClient;
  private string $userName;

  public function __construct() {
    $this->httpClient = new Client([
      'base_uri' => 'https://api.groupw.ru/',
      'headers' => [
        'X-Auth-Token' => env('GW_AUTH_TOKEN'),
        'X-Auth-Platform' => env('GW_AUTH_PLATFORM')
      ],
      'verify' => false
    ]);

    $container = Container::getInstance();

    /** @var UserEntity $user */
    $user = $container->get('currentUser');
    $this->userName = "{$user->firstName} {$user->lastName}";
  }

  public function getLeads() {
    try {
      $response = $this->httpClient->get('/tomsk/demands/se/lead', [ 'query' => [ 'date' => '20231114']]);
      $data = json_decode($response->getBody(), true);
      $tickets = $this->filterTickets($data);
    } catch (GuzzleException $e) {
      $tickets = [];
    }

    return $tickets;
  }

  public function getCustomers() {
    try {
      $response = $this->httpClient->get('/tomsk/demands/se/lead', [ 'query' => [ 'date' => '20231114']]);
      $data = json_decode($response->getBody(), true);
      $tickets = $this->filterTickets($data);
    } catch (GuzzleException $e) {
      $tickets = [];
    }

    return $tickets;
  }

  private function filterTickets($allTickets) {
    if ($allTickets['statusCode'] == 200) {
      return array_filter($allTickets['demands'], function ($ticket) {
        return $ticket['service_engineer'] == $this->userName;
      });
    }
    return [];
  }
}