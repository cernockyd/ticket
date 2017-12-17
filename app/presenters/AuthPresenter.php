<?php

namespace App\Presenters;

use App\Lib\Validate;
use App\Lib\Input;
use App\Lib\Token;
use App\Lib\Redirect;
use App\Models\User;
use App\Lib\Config;
use League\Plates\Engine as Template;

class AuthPresenter
{
  private $_results = array(),
          $_user;

  public function __construct() {
    $this->_user = new User();
  }

  public function login() {
    if (Input::check_form()) {
      $validate = new Validate();
      $validation = $validate->check($_POST, array(
        'name' => array(
          'required' => true,
          'max' => 100,
        ),
        'password' => array(
          'required' => true,
          'max' => 100,
        )
      ));
      if ($validation->passed()) {
        $this->ldap_auth();
      } else {
        $this->addResult('validation', $validation->errors());
      }
      $this->addResult('csrf_token', Token::generate());
    }
    $this->presentResult();
  }

  public function logout() {
    $user = new User();
    $user->logout();
    Redirect::to('/');
  }

  private function ldap_auth() {
    $user  = Input::get('name');
    $pass = Input::get('password');

    if (Config::get('env/mode') == 'production'
      && function_exists('radius_auth_open')
      && function_exists('radius_add_server')
      && function_exists('radius_create_request')
      && function_exists('radius_put_attr')
      && function_exists('radius_put_string')
      && function_exists('radius_send_request')
      && function_exists('radius_strerror')) {
      $radius = radius_auth_open();
      if (!radius_add_server($radius,Config::get('ldap/server'),0,Config::get('ldap/secret'),5,3)) {
        $this->addResult('error', 'Nepovedlo se připojit ke školnímu serveru.');
      }
      if (!radius_create_request($radius,RADIUS_ACCESS_REQUEST)) {
        $this->addResult('error', 'Nepovedlo se připojit ke školnímu serveru.');
      }
      radius_put_attr($radius,RADIUS_USER_NAME, $user);
      radius_put_attr($radius,RADIUS_CALLED_STATION_ID, Config::get('ldap/station_id'));
      radius_put_attr($radius,RADIUS_USER_PASSWORD, $pass);
      switch (radius_send_request($radius)) {
        case RADIUS_ACCESS_ACCEPT:
          if (!preg_match("/[0-9]+/", Input::get('name'))) {
            $user = new User(Input::get('name'));
            if (!$user->exists()) {
              $user_id = $user->create([
                'name_ldap' => Input::get('name'),
                'joined' => date('Y-m-d H:i:s'),
                'group_id' => 1]);
            } else {
              $user_id = $user->data()->id;
            }
            $login = $user->login($user_id, true);
            if($login) {
              $this->addResult('redirect', '/');
            }
          } else {
            $this->addResult('error', 'Nejste zaměstnanec školy.');
          }
          break;
        case RADIUS_ACCESS_REJECT:
          $this->addResult('error', 'Špatně zadané jméno nebo heslo.');
          break;
        case RADIUS_ACCESS_CHALLENGE:
          $this->addResult('error', 'Nepovedlo se přihlásit.');
          break;
        default:
          $this->addResult('error', radius_strerror($radius));
      }
    } elseif(Config::get('env/mode') == 'developement') {
      $user = new User(Input::get('name'));
      if (!$user->exists()) {
        $user_id = $user->create([
          'name_ldap' => Input::get('name'),
          'joined' => date('Y-m-d H:i:s'),
          'group_id' => 1]);
      } else {
        $user_id = $user->data()->id;
      }
      $login = $user->login($user_id, true);
      if($login) {
        $this->addResult('redirect', '/');
      }
    } else {
      $this->addResult('error', 'Nepovedlo se připojit ke školnímu serveru');
    }
  }

  public function renderLoginPage() {
    $t = new Template(__DIR__ . '/templates', 'tpl');
    $t->addData([
      'slug'=>'/login',
      'title'=>'Přihlášení',
      'user'=>$this->_user,
      'csrf_token'=>Token::generate()
    ]);
    echo $t->render('partials/Header');
    echo $t->render('Login');
    echo $t->render('partials/Footer');
  }

  public function addResult($type, $result) {
    if (!array_key_exists($type, $this->_results)) {
      $this->_results[$type] = [];
    }
    array_push($this->_results[$type], $result);
  }

  public function presentResult() {
    echo json_encode($this->_results);
  }

}