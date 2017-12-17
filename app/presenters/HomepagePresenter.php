<?php

namespace App\Presenters;

use App\Lib\DB;
use App\Lib\Token;
use App\Lib\Config;
use App\Models\User;
use League\Plates\Engine as Template;

class HomepagePresenter
{
  private $_user,
          $_db;

  public function __construct() {
    $this->_user = new User();
    $this->_db = DB::getInstance();
  }

  public function render() {
    if ($this->_user->isLoggedIn()) {
      if ($this->_user->isAdmin()) {
        $template = 'Dashboard';
      } elseif ($this->_user->isComplete()) {
        $template = 'UserDashboard';
      } else {
        $template = 'StepSettings';
      }
    } else {
      $template = 'Homepage';
    }
    $t = new Template(__DIR__ . '/templates', 'tpl');
    $t->addData([
      'slug'=> ($this->_user->isComplete()) ? '/d' : '/',
      'title'=> 'Přehled závad',
      'user'=>$this->_user,
      'user_hash'=>(!empty($this->_user->data()->email)) ? md5($this->_user->data()->email) : '',
      'url'=>Config::get('env/address'),
      'csrf_token'=>Token::generate(),
    ]);
    $r = $t->make('partials/Header');
    $r .= $t->make($template);
    $r .= $t->make('partials/Footer');
    echo $r;
  }

}