<?php

namespace App\Lib;

class Config
{

  public static function get($path) {
    if ($path) {
      $config = $GLOBALS['config'];
      $path = explode('/', $path);

      foreach ($path as $k) {
        if (isset($config[$k])) {
          $config = $config[$k];
        }
      }

      return $config;
    }
  }

}