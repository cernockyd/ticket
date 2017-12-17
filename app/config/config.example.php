<?php

$GLOBALS['config'] = [
  'mysql' => [
    'host' => 'HOST',
    'username' => 'USERNAME',
    'password' => 'PASSWORD',
    'db' => 'DBNAME',
  ],
  'ldap' => [
    'server' => 'IP ADDRESS',
    'station_id' => 'IP ADDRESS',
    'secret' => 'RANDOM'
  ],
  'env' => [
    'mode' => 'developement', // developement || production
    'address' => 'http://example.com/',
    'email' => 'noreply@example.com',
  ],
  'remember' => array(
    'cookie_name' => 'hash',
    'cookie_expiry' => 604800
  ),
  'session' => array(
    'session_name' => 'user',
    'token_name' => 'token',
  ),
];