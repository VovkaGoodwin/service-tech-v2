<?php

namespace App\Services;

use App\Repositories\TicketsRepository;

class TicketsService {
  public function getTickets() {
    $repository = new TicketsRepository();

    return [
      'new' => $repository->getLeads(),
      'old' => $repository->getCustomers(),
    ];
  }
}