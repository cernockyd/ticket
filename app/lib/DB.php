<?php

namespace App\Lib;

use PDO;
use PDOException;

class DB {

	private static $_instance = null;
	private $_pdo,
			$_query,
			$_error = false,
			$_results,
			$_count = 0;

	private function __construct() {
		try {
			$this->_pdo = new PDO('mysql:'. ('host='.Config::get('mysql/host') ). ';charset=utf8;dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public static function getInstance() {
		if(!isset(self::$_instance)) {
			self::$_instance = new DB();
		}
		return self::$_instance;
	}

	public function query($sql, $params = array(), $bind = []) {
		$this->_error = false;
		if (!empty($bind)) {
			if (!empty($bind['order'])) {
				$sql = $sql." ORDER BY ".$bind['order'];
			}
			if (!empty($bind['limit']) && is_numeric($bind['limit'])) {
				$sql = $sql." LIMIT ".$bind['limit'];
			}
			if (!empty($bind['offset']) && is_numeric($bind['offset'])) {
				$sql = $sql." OFFSET ".$bind['offset'];
			}
		}
		if($this->_query = $this->_pdo->prepare($sql)) {
			$x = 1;
			if(count($params)) {
				foreach($params as $param) {
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}
			if ($this->_query->execute()) {
				$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount();
			} else {
				$this->_error = true;
				print_r($this->_query->errorInfo());
				exit();
			}
		}

		return $this;
	}

	public function error() {
		return $this->_error;
	}

	public function count() {
		return $this->_count;
	}

	public function results() {
		return $this->_results;
	}

	public function first() {
		return $this->results()[0];
	}

	public function last($table) {
		return $this->_pdo->lastInsertId($table);
	}

	public function action($action, $table, $where = array(), $bind = []) {
		if(count($where) === 3) {
			$operators = array('=', '!=', '>', '<', '>=', '<=');

			$field = $where[0];
			$operator = $where[1];
			$value = $where[2];

			if(in_array($operator, $operators)) {
				$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

				if(!$this->query($sql, array($value), $bind)->error()) {
					return $this;
				}
			}
		}
		return false;
	}

	public function get($table, $where, $bind = null) {
		if (!$bind) {
			return $this->action('SELECT *', $table, $where);
		} else {
			return $this->action('SELECT *', $table, $where, $bind);
		}
	}

	public function delete($table, $where) {
		return $this->action('DELETE', $table, $where);
	}

	public function insert($table, $fields = []) {
		$keys = array_keys($fields);
		$values = null;
		$x = 1;
		$fields_count = count($fields);
		for ( $i = 0; $i < $fields_count; $i++ ) {
			$values .= "?";
			if ($x < $fields_count) {
				$values .= ', ';
			}
			$x++;
		}

		$sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";

		if(!$this->query($sql, $fields)->error()) {
			return true;
		}
		return false;
	}

	public function update($table, $id, $fields = []){
		$set = '';
		$x = 1;

		foreach($fields as $name => $value) {
			$set .= "{$name} = ?";
			if($x < count($fields)) {
				$set .= ',';
			}
			$x++;
		}

		$sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";

		if(!$this->query($sql, $fields)->error()) {
			return true;
		}

		return false;
	}

}
