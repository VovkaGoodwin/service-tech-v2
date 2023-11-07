<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class StaticController {
  public function renderSite(ServerRequestInterface $req, ResponseInterface  $resp) {
    ob_start();
    require_once STATIC_FILES . "/index.html";
    $body = $resp->getBody();
    $body->write(ob_get_clean());
    return $resp->withBody($body);
  }
}