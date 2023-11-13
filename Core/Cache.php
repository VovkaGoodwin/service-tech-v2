<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 15.09.2019
 * Time: 12:45
 */

namespace Core;


class Cache
{
  private static $instance;

  private function __construct() {

  }

  public function set($key, $data, $seconds  = 3600){
    if ($seconds) {
      $content['data'] = $data;
      $content['end_time'] = time() + $seconds;
      if (file_put_contents(CACHE.'/'.md5($key).'.txt',serialize($content))) {
        return TRUE;
      }
    }
    return FALSE;
  }

  public function get($key) {
    $file = CACHE.'/'.md5($key).'.txt';
    if (file_exists($file)) {
      $content = unserialize(file_get_contents($file));
      if (time() <= $content['end_time']) {
        return $content['data'];
      } else {
        unlink($file);
      }
    }
    return FALSE;
  }

  public function delete($key) {
    $file = CACHE.'/'.md5($key).'.txt';
    if (file_exists($file)) {
      unlink($file);
    }
  }

  public static function getInstance() {
    if (empty(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }
}