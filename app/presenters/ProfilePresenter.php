<?php

namespace App\Presenters;

use App\Lib\Validate;
use App\Lib\Input;
use App\Lib\Token;
use App\Lib\Redirect;
use App\Models\User;
use League\Plates\Engine as Template;

class ProfilePresenter
{
  private $_results = array(),
          $_user;

  public function __construct() {
    $this->_user = new User();
  }


  public function save_settings() {
    $user = new User();
    if (!$user->isLoggedIn()) {
      header('HTTP/1.0 401 Unauthorized');
      exit();
    }

    if (Input::check_form()) {
      $validate = new Validate();
      $validation = $validate->check($_POST, [
        'surname' => array('test' => true),
        'name' => [
          'max' => 100,
          'required' => true,
        ],
        'email' => [
          'required' => true,
          'min' => 2,
          'max' => 254,
          'email' => true
        ],
        'notif-allow' => [
          'max' => 1,
        ],
        'notif-change' => [
          'max' => 1,
        ],
        'notif-solve' => [
          'max' => 1,
        ],
        'notif-comments' => [
          'max' => 1,
        ],
      ]);

      if ($validation->passed()) {
        $new_settings = [];
        $settings = [
          'notif-allow' => Input::get('notif-allow'),
          'notif-change' => Input::get('notif-change'),
          'notif-solve' => Input::get('notif-solve'),
          'notif-comments' => Input::get('notif-comments'),
        ];
        foreach ($settings as $setting => $v) {
          if (!empty($v)) {
            $new_settings[$setting] = true;
          } else {
            $new_settings[$setting] = false;
          }
        }
        $user->update([
          'name' => Input::get('name'),
          'email' => Input::get('email'),
          'settings' => json_encode($new_settings)
        ]);
        $this->addResult('success_message', 'Nastavení uloženo.');
      } else {
        $this->addResult('validation', $validation->errors());
      }
      $this->addResult('csrf_token', Token::generate());
    }
    $this->presentResult();
  }

  public function save_step_settings() {
    $user = new User();
    if (!$user->isLoggedIn()) {
      header('HTTP/1.0 401 Unauthorized');
      exit();
    }

    if (Input::check_form()) {
      $validate = new Validate();
      $validation = $validate->check($_POST, [
        'surname' => array('test' => true),
        'name' => [
          'max' => 100,
          'required' => true,
        ],
        'email' => [
          'required' => true,
          'min' => 2,
          'max' => 254,
          'email' => true
        ],
      ]);

      if ($validation->passed()) {
        $new_settings = [];
        $settings = [
          'notif-allow' => 1,
          'notif-change' => 0,
          'notif-solve' => 1,
          'notif-comments' => 1,
        ];
        foreach ($settings as $setting => $v) {
          if (!empty($v)) {
            $new_settings[$setting] = true;
          } else {
            $new_settings[$setting] = false;
          }
        }
        $user->update([
          'name' => Input::get('name'),
          'email' => Input::get('email'),
          'settings' => json_encode($new_settings)
        ]);
        $this->addResult('redirect', '/');
      } else {
        $this->addResult('validation', $validation->errors());
      }
      $this->addResult('csrf_token', Token::generate());
    }
    $this->presentResult();
  }

  public function renderProfileSettingsPage() {
	  $settings_arr = '';
    if ($this->_user->isLoggedIn()) {
      $settings_labels = [
        'notif-allow' => 'Přeji si získávat oznámení emailem',
        'notif-change' => 'Získávat oznámení o průběžné změně stavu',
        'notif-solve' => 'Získávat oznámení o vyřešení závady',
        'notif-comments' => 'Získávat oznámení o komentářích'
      ];

      if (!empty($this->_user->data()->settings)) {
        $settings_arr = json_decode($this->_user->data()->settings, true);
        foreach ($settings_arr as $key => $value) {
          $settings_arr[$key] = [
            'label' => $settings_labels[$key],
            'value' => $value,
          ];
        }
      }

      $t = new Template(__DIR__ . '/templates', 'tpl');
      $t->addData([
        'slug'=>'/settings',
        'title'=>'Nastavení profilu',
        'settings_arr'=>$settings_arr,
        'user'=>$this->_user,
        'csrf_token'=>Token::generate()
      ]);
      echo $t->render('partials/Header');
      echo $t->render('ProfileSettings');
      echo $t->render('partials/Footer');
    } else {
      Redirect::to('/');
    }
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