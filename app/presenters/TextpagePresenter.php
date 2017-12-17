<?php

namespace App\Presenters;

use App\Lib\Token;
use App\Models\User;
use League\Plates\Engine as Template;

class TextpagePresenter
{
  private $_user;

  public function __construct() {
    $this->_user = new User();
  }

  public function render_404($template = '404') {
    $t = new Template(__DIR__ . '/templates', 'tpl');
    $t->addData(['slug'=>'/404','title'=>'Stránka nenalezena','user'=>$this->_user,'csrf_token'=>Token::generate()]);
    echo $t->render('partials/Header');
    echo $t->render('other/'.$template);
    echo $t->render('partials/Footer');
  }

  public function render_about($template = 'About') {
    $t = new Template(__DIR__ . '/templates', 'tpl');
    $t->addData(['slug'=>'/about','title'=>'O službě','user'=>$this->_user,'csrf_token'=>Token::generate()]);
    echo $t->render('partials/Header');
    echo $t->render($template);
    echo $t->render('partials/Footer');
  }

  public function render_faq($template = 'Faq') {
    $t = new Template(__DIR__ . '/templates', 'tpl');
    $t->addData(['slug'=>'/jak-to-funguje','title'=>'Jak to funguje','user'=>$this->_user,'csrf_token'=>Token::generate()]);
    echo $t->render('partials/Header');
    echo $t->render($template);
    echo $t->render('partials/Footer');
  }

}