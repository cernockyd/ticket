<?php
namespace App\Models;

use App\Lib\DB;
use Exception;

class Card {

  private $_db,
          $_data;

  public function __construct($id = null) {
    $this->_db = DB::getInstance();
    if($id) {
      $this->find($id);
    }
  }

  public function update($fields = [], $id = null) {
    if (!$id) {
      $id = $this->data()->id;
    }
    if (!$this->_db->update('cards', $id, $fields)) {
      throw new Exception('There was problem updating card.');
    }
  }

  public function create($fields = []) {
    if (!$this->_db->insert('cards', $fields)) {
      throw new Exception('There was a problem creating card.');
    } else {
      $product_id = $this->_db->last('cards');
      return $product_id;
    }
  }

  public function find($id = null) {
    if (!empty($id) && is_numeric($id)) {
      $data = $this->_db->get('cards', ['id', '=', $id]);
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

  public static function get_photo_url($id) {
    if (!empty($id)) {
      $db = DB::getInstance();
      $data = $db->get('media', array('id', '=', $id));
      if ($data->count()) {
        $media_data = $data->first();
	      return 'p/photo/' . $media_data->link;
      }
    }
    return false;
  }

  public static function get_taxonomy($id) {
    if (!empty($id)) {
      $db = DB::getInstance();
      $data = $db->get('taxonomies', array('id', '=', $id));
      if ($data->count()) {
        $d = $data->first();
	      return $d->singular;
      }
    }
    return false;
  }

}