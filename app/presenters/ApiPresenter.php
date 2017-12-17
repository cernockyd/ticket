<?php
namespace App\Presenters;

use DateTime;
use App\Lib\DB;
use App\Lib\Config;
use App\Lib\Input;
use App\Lib\Email;
use App\Models\User;
use App\Models\Card;
use App\Models\Activity;
use League\Plates\Engine as Template;

class ApiPresenter
{
  private $_user,
          $_db;

  public function __construct() {
    $this->_user = new User();
    $this->_db = DB::getInstance();
  }


  /**
   * [POST]
   * create activity by type
   * @return int $activity_id or 'false'
   */
  public function create_activity() {
    $type = Input::get('type');
    $card_id = Input::get('card_id');
    $force_email = Input::get('force_email');
    $message = Input::get('message');
    $archived = Input::get('archived');
    $card_state_id = Input::get('card_state_id');

    $card = new Card($card_id);
    if (!empty($type) && $type == 'state' && !empty($card_state_id)) {
      if ($card_state_id == 3 && !empty($archived)) {
        $this->update_card_state($card, 3, 1);
      } else {
        $this->update_card_state($card, $card_state_id, 0);
      }
    }

    $force_email = (!empty($force_email)) ? $force_email : null;
    if ($this->_user->isLoggedIn() && $this->check_card($card)) {
      $activity = new Activity();
      $activity_params = [
        'user_id' => $this->_user->data()->id,
        'card_id' => $card_id,
        'added' => date('Y-m-d H:i:s'),
        'edited' => null,
        'card_state_id' => 0,
        'message_text' => '',
      ];
      if (!empty($message)) {
        if (strlen($message) > 800) {
          $this->present_json(['error' => 'Komentář může mít maximálně 800 znaků']);
          exit();
        }
      }
      switch ($type) {
        case 'state':
          if ((int)$card_state_id == 3) {
            $activity_params['card_state_id'] = $card_state_id;
            $activity_params['message_text'] = $message;
            $activity_params['type_id'] = 2;
          } else {
            $activity_params['type_id'] = 1;
            $activity_params['card_state_id'] = $card_state_id;
            if (!empty($message)) {
              $activity_params['message_text'] = $message;
            }
          }
          break;
        case 'comment':
          $activity_params['type_id'] = 3;
          $activity_params['message_text'] = $message;
          break;
      }
      if ($card->data()->state_id == $card_state_id) {
        echo 'false';
        exit();
      }
      if ($activity_id = $activity->create($activity_params)) {
        $card_owner = new User($card->data()->user_id);
        if ($card_owner->exists() && $type == 'comment' OR $card_owner->exists()) {
          if ($force_email OR $card_owner->authorizeNotification('notif-comments') && $card_owner->authorizeNotification('notif-allow')) {

            $t = new Template(__DIR__ . '/templates', 'tpl');
            $t->addData([
              'subject'=>$card->data()->name,
              'description'=>$card->data()->description,
              'type'=>'Nový komentář u požadavku',
              'button_text'=>'Detail požadavku',
              'card_id'=>$card_id,
              'message'=>'<strong style="color:#000;">Komentář:</strong><br>'.e($message).'<br>',
              'url'=>Config::get('env/address')
            ]);
            $email_message = $t->render('emails/Card', ['title'=>'Nový komentář u požadavku'.': '.e($card->data()->name)]);

            if (!$this->_user->isAdmin()) {
                Email::send($card_owner->data()->email, $this->_user->data()->name, Config::get('env/email'), 'Nový komentář u požadavku: '.e($card->data()->name), $email_message);
            } else {
              $admins = $this->_db->get('users', ['group_id', '=', 2]);
              if (!empty($admins) && $admins->count()) {
                foreach ($admins->results() as $admin) {
                  Email::send($admin->email, $this->_user->data()->name, Config::get('env/email'), 'Nový komentář u požadavku: '.e($card->data()->name), $email_message);
                }
              }
            }
          }
        } elseif ($card_owner->exists() && $type == 'state') {
        	$state_mess = '';
        	$notif_setting = '';
          switch ($card_state_id) {
            case 1:
              $notif_setting = 'notif-change';
              $state_mess = 'Správce sítě si zobrazil Váš požadavek';
              break;
            case 2:
              $notif_setting = 'notif-change';
              $state_mess = 'Váš požadavek je nyní v řešení';
              break;
            case 3:
              $notif_setting = 'notif-solve';
              $state_mess = 'Váš požadavek byl vyřešen';
              break;
          }
          if ($card_owner->authorizeNotification('notif-allow') && $card_owner->authorizeNotification($notif_setting)) {
            $t = new Template(__DIR__ . '/templates', 'tpl');
            $t->addData([
              'subject'=>$card->data()->name,
              'description'=>$card->data()->description,
              'type'=>$state_mess,
              'button_text'=>'Detail požadavku',
              'card_id'=>$card_id,
              'message'=>'',
              'url'=>Config::get('env/address')
            ]);
            if (!empty($message)) { // if state change has message
              $t->addData([
                'message'=>'<strong style="color:#000;">Komentář:</strong><br>'.e($message).'<br>',
              ]);
            }
            $email_message = $t->render('emails/Card', ['title'=>$state_mess.': '.e($card->data()->name)]);
            $card_owner->send_email($state_mess, $email_message);
          }
        }

        $activity->find($activity_id);
        if ($activity->exists()) {
          $new_c = [
            'id'=>(int)$activity->data()->id,
            'user_id'=>(int)$activity->data()->user_id,
            'type_id'=>(int)$activity->data()->type_id,
            'card_state_id'=>(int)$activity->data()->card_state_id,
            'message'=>e($activity->data()->message_text),
            'date'=> (!empty($activity->data()->edited)) ? $activity->data()->edited : $activity->data()->added,
            'edited'=> (!empty($activity->data()->edited)) ? true : false,
          ];
          $this->present_json($new_c);
        }
      }
    }
    echo 'false';
    exit();
  }


  /**
   * [POST]
   */
  public function delete_activity() {
    $id = Input::get('id');
    $activity = new Activity($id);
    if ($this->_user->isLoggedIn() && $this->check_activity($activity)) {
      $activity->delete();
      echo 'true';
      exit();
    }
    echo 'false';
  }


  public function update_activity() {
    $id = Input::get('id');
    $message = Input::get('message');
    $activity = new Activity($id);

    if ($this->_user->isLoggedIn() && $this->check_activity($activity)) {
      switch ($activity->data()->type_id) {
        case 2:
        case 3:
          $activity->update([
            'message' => $message,
            'edited' => date('Y-m-d'),
          ]);
          break;
      }
      echo 'true';
      exit();
    }
    echo 'false';
  }


  /**
   * [GET]
   * @param  int $offset
   * @param  string $labels  1&2&3
   * @param  int $limit limit of cards to get
   */
  public function get_cards($offset = 0, $labels = 'all', $limit = 16) {
    $new_cards = [];
    $user_sql = '';
    if (!$this->_user->isLoggedIn()) {
      header('HTTP/1.0 401 Unauthorized');
      exit();
    }
    if ($offset == 'export') {
      $limit = 0;
    }
    if (!$this->_user->isAdmin()) {
      $user_sql = 'AND user_id = '.$this->_user->data()->id;
    }
    if (isset($offset) && $labels != 'all') {
      $labels_arr = explode('&', $labels);
      if (array_filter($labels_arr, 'is_numeric')) {
        $sql_labels = '';
        $labels_count = count($labels_arr);
        for ($i=1; $i <= $labels_count; $i++) {
          $name = 't'.$i;
          $sql_labels .= ' JOIN cards_taxonomies '.$name.' ON '.$name.'.taxonomy_id=? AND c.id='.$name.'.card_id';
        }
        $sql = 'SELECT distinct c.* FROM cards c'.$sql_labels.' WHERE archived = 0 '.$user_sql;
        $data = $this->_db->query($sql, $labels_arr, ['limit'=>$limit,'offset'=>$offset,'order'=>'id ASC']);
      } elseif ($labels == 'archive') {
        $data = $this->_db->query('SELECT * FROM cards WHERE archived = 1 '.$user_sql, [], ['limit'=>$limit,'offset'=>$offset,'order'=>'id ASC']);
      }
    } else {
       $data = $this->_db->query('SELECT * FROM cards WHERE archived = 0 '.$user_sql, [], ['limit'=>$limit,'offset'=>$offset,'order'=>'id ASC']);
    }
    if (!empty($data) && $data->count()) {
      $today = date('Y-m-d');
      $users = [];
      foreach ($data->results() as $c) {
        $taxonomies = $this->_db->query('SELECT t.taxonomy_id AS id FROM cards_taxonomies t WHERE card_id = ?', [$c->id]);
        $taxonomies_ar = [];
        if ($taxonomies->count()) {
          foreach ($taxonomies->results() as $t) {
            array_push($taxonomies_ar, (int)$t->id);
          }
        }
        $added = new DateTime($c->added);
        $diff = strtotime($today) - strtotime($added->format('Y-m-d'));
        $days = (int)$diff/(60*60*24);
        $new_c = [
          'id' => (int)$c->id,
          'name' => e($c->name),
          'description' => e($c->description),
          'date' => $c->added,
          'old' => round($days, 0, PHP_ROUND_HALF_DOWN),
          'state_id' => (int)$c->state_id,
          'user_id' => (int)$c->user_id,
          'archived' => (int)$c->archived,
          'taxonomies' => $taxonomies_ar,
        ];
        array_push($new_cards, $new_c);
      }
      $this->present_json($new_cards);
      exit();
    }
    echo 'false';
  }


  /**
   * [GET]
   * @param  string $cats_string category ids separated by &
   */
  public function get_categories($cats_string) {
    if ($this->_user->isLoggedIn()) {
      $result = [];
      $cats = explode('&', $cats_string);
      $bindvalue = $this->get_bind_value($cats);
      $data = $this->_db->query('SELECT distinct * FROM taxonomies WHERE id in ('.$bindvalue.')', $cats);
      if (!empty($data) && $data->count()) {
        foreach ($data->results() as $t) {
          array_push($result, $t);
        }
	      $this->present_json($result);
      }
    }
  }


  /**
   * [GET]
   * @param  string $users_string user ids separated by &
   */
  public function get_users($users_string) {
    if ($this->_user->isLoggedIn()) {
      $result = [];
      $users = explode('&', $users_string);
      $bindvalue = $this->get_bind_value($users);
      $data = $this->_db->query('SELECT distinct u.id, u.name, u.group_id, u.email FROM users u WHERE id in ('.$bindvalue.')', $users);
      if (!empty($data) && $data->count()) {
        foreach ($data->results() as $t) {
          $new_t = (object) [
            'id' => $t->id,
            'name' => $t->name,
            'group_id' => $t->group_id,
            'hash' => md5($t->email),
          ];

          array_push($result, $new_t);
        }
        $this->present_json($result);
      }
    }
  }


  /**
   * [GET]
   * @param  int $id
   */
  public function get_card($id) {
    if (!empty($id)) {
      $card = new Card($id);
      if ($card->exists()) {
        $new_c = [
          'name'=>e($card->data()->name),
          'description'=>e($card->data()->description),
          'state_id'=>(int)$card->data()->state_id,
          'archived'=>(int)$card->data()->archived,
        ];
        echo json_encode($new_c);
        exit();
      }
    }
    echo 'false';
  }


  /**
   * [GET]
   * @param  int $id
   * @param  int $offset
   */
  public function get_card_activities($id, $offset = 0) {
    $limit = 10;
    $card = new Card($id);
    $new_ar = [];
    if ($this->check_card($card)) {
      $sql = 'SELECT distinct a.* FROM activities a WHERE card_id = ?';
      $data = $this->_db->query($sql, [$id], ['limit'=>$limit,'offset'=>$offset,'order'=>'id DESC']);
      if (!empty($data) && $data->count()) {
        foreach ($data->results() as $a) {
          $edited = $a->edited;
          $new_c = [
            'id'=>(int)$a->id,
            'user_id'=>(int)$a->user_id,
            'type_id'=>(int)$a->type_id,
            'card_state_id'=>(int)$a->card_state_id,
            'message'=>e($a->message_text),
            'date'=> (!empty($edited)) ? $edited : $a->added,
            'edited'=> (!empty($edited)) ? true : false,
          ];
          array_push($new_ar, $new_c);
        }
      }
      $data_count = $data->count();
      if (empty($data_count) OR $data_count < $limit) {
        $created = [
          'id'=>0,
          'user_id'=>$card->data()->user_id,
          'date'=> $card->data()->added,
          'type_id'=>0,
        ];
        array_push($new_ar, $created);
      }
      echo json_encode($new_ar);
      exit();
    }
    echo 'false';
  }

  /**
   * Returns notifications by date
   * [GET]
   * @param int $cards_offset
   * @param int $activities_offset
   * @param int $dose number of returned notifications
   */
  public function get_notifications_for_user($cards_offset, $activities_offset, $dose = 10) {
    $new_ar = [];
    $offset = [];
    if ($this->_user->isLoggedIn()) {
      $user_id = $this->_user->data()->id;
      if (isset($cards_offset) && isset($activities_offset)) {
        $offset = [
          'card' => $cards_offset,
          'activity' => $activities_offset
        ];
      }
      $sql = [
        'card' => 'SELECT DISTINCT * FROM cards WHERE user_id != ?',
        'activity' => 'SELECT DISTINCT * FROM activities WHERE user_id != ?',
      ];

      foreach ($sql as $type => $sql_code) {
        $data = $this->_db->query($sql_code, [$user_id], ['limit'=>$dose,'offset'=>$offset[$type],'order'=>'id DESC']);
        if (!empty($data) && $data->count()) {
          foreach ($data->results() as $a) {
            $new_c = [
              'type'=>$type,
              'user_id'=>(int)$a->user_id,
              'date'=>$a->added,
              'seen'=>0,
            ];
            if (!empty($a->seen) || !empty($a->state_id) && $a->state_id != 0) {
              $new_c['seen'] = 1;
            }
            if (!empty($a->type_id)) {
              switch ($a->type_id) {
                case 1:
                  $new_c['type'] = 'solve';
                  break;
                case 2:
                  $new_c['type'] = 'state';
                  break;
                case 3:
                  $new_c['type'] = 'comment';
                  break;
              }
              $activity_card = new Card($a->card_id);
              $new_c['name'] = $activity_card->data()->name;
            }
            if (!empty($a->card_id)) {
              $new_c['id'] = $a->card_id;
            } else {
              $new_c['id'] = $a->id;
            }
            if (!empty($a->name)) {
              $new_c['name'] = $a->name;
            }
            array_push($new_ar, $new_c);
          }
        }
      }
      if (!empty($new_ar)) {
        usort($new_ar, "dateSort");
        $slice_ar = array_slice($new_ar, 0, 10);
        echo json_encode($slice_ar);
        exit;
      }
    }
    echo 'false';
  }


  /**
   * [GET]
   * @param  string $labels  1&2&3
   */
  public function get_cards_count($labels = 'all') {
    $user_sql = '';
    if (!$this->_user->isAdmin()) {
      $user_sql = 'AND user_id = '.$this->_user->data()->id;
    }
    if ($labels != 'all') {
      $labels_arr = explode('&', $labels);
      if (array_filter($labels_arr, 'is_numeric')) {
        $sql_labels = '';
        $labels_count = count($labels_arr);
        for ($i=1; $i <= $labels_count; $i++) {
          $name = 't'.$i;
          $sql_labels .= ' JOIN cards_taxonomies '.$name.' ON '.$name.'.taxonomy_id=? AND c.id='.$name.'.card_id';
        }
        $sql = 'SELECT distinct c.id FROM cards c'.$sql_labels.' WHERE archived = 0 '.$user_sql;
        $data = $this->_db->query($sql, $labels_arr, []);
      } elseif ($labels == 'archive') {
        $data = $this->_db->query('SELECT * FROM cards WHERE archived = 1 '.$user_sql, [], []);
      }
    } else {
       $data = $this->_db->query('SELECT * FROM cards WHERE archived = 0 '.$user_sql, [], []);
    }
    if (!empty($data) && $data->count()) {
      echo $data->count();
      exit();
    }
    echo 'false';
  }


  /**
   * [POST]
   */
  public function update_card() {
    $id = Input::get('id');
    $col = Input::get('col');
    $value = Input::get('value');
    $card = new Card($id);

    if ($this->check_card($card)) {
      switch ($col) {
          case 'description':
          case 'name':
            $card->update([$col=>$value]);
            echo 'true';
            exit();
            break;
        }
    }
    echo 'false';
  }


  private function update_card_state($card, $state_id = null, $archived = null) {
    if ($card->exists() && !empty($state_id) && in_array($state_id, [1,2,3])) {
      $old_state = $card->data()->state_id;
      $new_user = new User($card->data()->user_id);
      if ($new_user->exists() && $old_state != $state_id) {
        if ($this->check_card($card)) {
          $params = [
            'state_id' => $state_id
          ];
          if (!empty($archived)) {
            $params['archived'] = 1;
          }
          $card->update($params);
        }
      }
    }
  }


  /**
   * [POST]
   * @param  int id
   * @param  int archived 0:not archived 1:archived
   */
  public function archive_card() {
    $id = Input::get('id');
    $archived = Input::get('archived');
    $card =  new Card($id);
    if ($this->check_card($card)) {
      $card->update(['archived'=>$archived]);
      echo 'true';
      exit;
    }
    echo 'false';
  }


  /**
   * [POST]
   * @param  int id
   * @param  int archived 0:not archived 1:archived
   */
  public function delete_card() {
    $id = Input::get('id');
    if (!empty($id)) {
      $card =  new Card($id);
      if ($this->check_card($card)) {
        $this->_db->delete('cards_taxonomies', ['card_id', '=', $id]);
        $this->_db->delete('cards', ['id', '=', $id]);
        echo 'true';
        exit;
      }
    }
    echo 'false';
  }


  /**
   * @param  array $array of values to bind
   * @return string of ? determinated by number of values
   */
  private function get_bind_value($array) {
    $bindvalue = '';
    if (array_filter($array, 'is_numeric')) {
      $ar_count = count($array);
      for ($i=1; $i <= $ar_count ; $i++) {
        if ($i != $ar_count) {
          $bindvalue .= '?,';
        } else {
          $bindvalue .= '?';
        }
      }
      return $bindvalue;
    } else {
      echo 'false';
      exit();
    }
  }


  /**
   * @param  int id
   * @return boolean
   */
  private function check_card($card) {
    if ($card->exists()) {
      if ($this->_user->data()->id == $card->data()->user_id || $this->_user->isAdmin()) {
        return true;
      }
    }
    return false;
  }

  /**
   * @param obj $activity
   * @return boolean
   */
  private function check_activity($activity) {
    if ($activity->exists()) {
      if ($this->_user->data()->id == $activity->data()->user_id) {
        return true;
      }
    }
    return false;
  }

  private function present_json($data) {
    echo json_encode($data);
    exit();
  }


  public function post_authorize() {
    if (!$this->_user->isLoggedIn()) {
      header('HTTP/1.0 401 Unauthorized');
      exit();
    }
  }

}