<?php
namespace App\Models;

use App\Lib\DB;
use Exception;

class Notification {

  private $_db,
          $_data;

  public function __construct($notification = null) {
    $this->_db = DB::getInstance();
    if($notification) {
      $this->find($notification);
    }
  }

  public function delete($id = null) {
    if (!$id) {
      $id = $this->data()->id;
    }
    if (!$this->_db->delete('notifications', ['id', '=', $id])) {
      throw new Exception('There was problem deleting notification.');
    }
  }

  public function create($fields = []) {
    if (!$this->_db->insert('notifications', $fields)) {
      throw new Exception('There was a problem creating notification.');
    } else {
      $notification_id = $this->_db->last('notifications');
      return $notification_id;
    }
  }

  public function find($notification = null) {
    if (!empty($notification) && is_numeric($notification)) {
      $data = $this->_db->get('cards', ['id', '=', $notification]);
      if ($data->count()) {
        $this->_data = $data->first();
        return true;
      }
    }
    return false;
  }

  public function exists() {
    return (!empty($this->_data)) ? true : false;
  }

  public function data() {
    return $this->_data;
  }

}