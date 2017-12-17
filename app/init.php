<?php

function e($d) {
  return htmlentities($d);
}

function dateSort($a,$b){
  $dateA = strtotime($a['date']);
  $dateB = strtotime($b['date']);
  return ($dateB-$dateA);
}

function idSort($a,$b){
  $idA = $a['id'];
  $idB = $b['id'];
  return ($idB-$idA);
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config/config.php';

spl_autoload_register(function($class) {
  $class = str_replace('\\', '/', $class);
  $parts = pathinfo($class);
  $file = __DIR__ .'/../' . strtolower($parts['dirname']) .'/'. $parts['filename'] . '.php';
  require_once $file;
});