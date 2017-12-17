<?php
namespace App\Models;

use App\Lib\DB;

class Activity {

  private $_db,
          $_data;

  public function __construct($activity = null) {
    $this->_db = DB::getInstance();
    if($activity) {
      $this->find($activity);
    }
  }

  public function delete($id = null) {
    if (!$id) {
      $id = $this->data()->id;
    }
    if (!$this->_db->delete('activities', ['id', '=', $id])) {
      throw new Exception('There was problem deleting activity.');
    }
  }

  public function create($fields = []) {
    if (!$this->_db->insert('activities', $fields)) {
      throw new Exception('There was a problem creating activity.');
    } else {
      $activity_id = $this->_db->last('activities');
      return $activity_id;
    }
  }

  public function find($activity = null) {
    if (!empty($activity) && is_numeric($activity)) {
      $data = $this->_db->get('activities', ['id', '=', $activity]);
      if ($data->count()) {
        $this->_data = $data->first();
        return true;
      }
    }
  }

	public function update($fields = array(), $id = null) {

    if(!$id) {
        $id = $this->data()->id;
    }

    if(!$this->_db->update('activities', $id, $fields)) {
        throw new Exception('There was problem updating user.');
    }
	}

  public function exists() {
    return (!empty($this->_data)) ? true : false;
  }

  public function data() {
    return $this->_data;
  }

}