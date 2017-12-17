<?php


namespace App\Presenters;

use App\Lib\Config;
use App\Lib\DB;
use App\Lib\Token;
use App\Lib\Input;
use App\Lib\Validate;
use App\Lib\Redirect;
use App\Lib\Session;
use App\Lib\Email;
use App\Models\User;
use App\Models\Card;
use League\Plates\Engine as Template;

class CardsPresenter
{

  private $_results = [],
          $_db,
          $_user;

  public function __construct() {
    $this->_user = new User();
    $this->_db = DB::getInstance();
  }

  private function add_card() {
    $card = new Card();
    $number = Input::get('number');
    $subject = Input::get('subject');
    $description = Input::get('description');
    $urgent = Input::get('urgent');

    if (!empty($number)) {
      $number = ' #'.$number;
    }
    $card_id = $card->create([
      'user_id' => $this->_user->data()->id,
      'added' => date('Y-m-d H:i:s'),
      'name' => $subject.$number,
      'description' => $description,
      'state_id' => 0,
      'archived' => 0,
    ]);
    $this->_db->insert('cards_taxonomies', ['card_id'=>$card_id, 'taxonomy_id'=>Input::get('category')]);
    if (!empty($urgent)) {
      $this->_db->insert('cards_taxonomies', ['card_id'=>$card_id, 'taxonomy_id'=>1]);
    }
    if (!empty($card_id)) {
      $admins = $this->_db->get('users', ['group_id', '=', 2]);
      if (!empty($admins) && $admins->count()) {
          $t = new Template(__DIR__ . '/templates', 'tpl');
          $t->addData([
            'subject'=> $subject,
            'description'=>$description,
            'type'=>'Nový požadavek',
            'button_text'=>'Detail požadavku',
            'card_id'=>$card_id,
            'message'=>'',
            'url'=>Config::get('env/address')
          ]);
          $message = $t->render('emails/Card', ['title'=>'Nový požadavek: '.$subject]);
        foreach ($admins->results() as $admin) {
          Email::send($admin->email, $admin->name, Config::get('env/email'), 'Nový požadavek: '.$subject, $message);
        }
      }
      $this->addResult('redirect', '/newitem/thanks');
    }
  }

  public function test() {
    $mess = 'zprava';
    $t = new Template(__DIR__ . '/templates', 'tpl');
    $t->addData([
      'subject'=>'Něco nefunguje',
      'description'=> 'furt',
      'type'=>'Nový komentář u požadavku',
      'button_text'=>'Detail požadavku',
      'card_id'=>32,
      'message'=>'<strong style="color:#000;">Komentář:</strong><br>'.e($mess).'<br><br>',
      'url'=>Config::get('env/address')
    ]);
    echo $t->render('emails/Card', ['title'=>'Vystavit předmět']);
  }

  public function add_card_form() {
    $this->post_authorize();

    if (Input::check_form()) {
      $validate = new Validate();
      $validate->check($_POST, [
        'surname' => array('test' => true),
        'description' => [
          'max' => 400
        ],
        'subject' => [
          'required' => true,
          'max' => 100,
        ],
        'urgent' => [
          'max' => 1,
        ],
        'category' => [
          'required' => true,
          'taxonomy' => true,
        ],
        'number' => [
          'integer' => true,
        ],
      ]);
      if ($validate->passed()) {
        $this->add_card();
      } else {
        $this->addResult('validation', $validate->errors());
      }
    }
    $this->addResult('csrf_token', Token::generate());
    $this->presentResult();
  }

  public function post_authorize() {
    if (!$this->_user->isLoggedIn()) {
      header('HTTP/1.0 401 Unauthorized');
      exit();
    }
  }

  public function random_string($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  public function renderAddPage() {
     if (!$this->_user->isLoggedIn()) {
      Redirect::to('/login');
      exit();
    }
    Session::delete('AddCard/Media');
    $t = new Template(__DIR__ . '/templates', 'tpl');
    $t->addData(['slug'=>'/newitem','user'=>$this->_user,'csrf_token'=>Token::generate()]);
    echo $t->render('partials/Header', ['title'=>'Vystavit předmět']);
    echo $t->render('AddCard');
    echo $t->render('partials/Footer');
  }

  public function renderThanksPage() {
     if (!$this->_user->isLoggedIn()) {
      Redirect::to('/login');
      exit();
    }
    Session::delete('AddCard/Media');
    $t = new Template(__DIR__ . '/templates', 'tpl');
    $t->addData(['slug'=>'/newitem','user'=>$this->_user,'csrf_token'=>Token::generate()]);
    echo $t->render('partials/Header', ['title'=>'Děkujeme']);
    echo $t->render('Thanks');
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