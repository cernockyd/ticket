<?php

namespace App\Lib;

class Token {

  public static function generate() {
    return Session::put(Config::get('session/token_name'), md5(uniqid()));
  }

  public static function check($token) {
    $tokenName = Config::get('session/token_name');
    $session_check = Session::exists($tokenName);
    $session = Session::get($tokenName);


    if ($session_check && $token === $session) {
      Session::delete($tokenName);
      return true;
    }

    return false;
  }

  public static function check_ajax($token) {
    $tokenName = Config::get('session/token_name');

    if(Session::exists($tokenName) && $token === Session::get($tokenName)) {
      return true;
    }

    return false;
  }

}