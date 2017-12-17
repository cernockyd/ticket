<?php
namespace App\Models;

use App\Lib\Config;
use App\Lib\Session;
use App\Lib\Hash;
use App\Lib\Cookie;
use App\Lib\DB;
use App\Lib\Email;
use Exception;

class User {

	private $_db,
			$_data,
			$_sessionName,
			$_cookieName,
			$_isLoggedIn;

	public function __construct($user = null) {
		$this->_db = DB::getInstance();

		$this->_sessionName = Config::get('session/session_name');
		$this->_cookieName = Config::get('remember/cookie_name');

		if(!$user) {
			if(Session::exists($this->_sessionName)) {
				$user = Session::get($this->_sessionName);

				if($this->find($user)) {
					$this->_isLoggedIn = true;
				} else {
					// Process Logout
				}

			}
		} else {
			$this->find($user);
		}

	}

	public function update($fields = array(), $id = null) {

		if(!$id) {
			$id = $this->data()->id;
		}

		if(!$this->_db->update('users', $id, $fields)) {
			throw new Exception('There was problem updating user.');
		}
	}

	public function create($fields = array()) {
		if(!$this->_db->insert('users', $fields)) {
			throw new Exception('There was a problem creating account.');
		} else {
			$user_id = $this->_db->last('users');
			return $user_id;
		}
	}

	public function find($user = null, $fb_id = null) {
		if ($user) {
			$field = (is_numeric($user)) ? 'id' : 'name_ldap';
			$data = $this->_db->get('users', array($field, '=', $user));
			if ($data->count()) {
				$this->_data = $data->first();
				return true;
			}
		} elseif ($fb_id) {
			$data = $this->_db->get('users', ['fb_id', '=', $fb_id]);
			if ($data->count()) {
				$this->_data = $data->first();
				return true;
			}
		}
		return false;
	}

	public function login($ldap_name = null, $remember = true) {
		$user = $this->find($ldap_name);
		if($user) {
			Session::put($this->_sessionName, $this->data()->id);
			if($remember) {
				$hash = Hash::unique();
				$hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));

				if(!$hashCheck->count()) {
					$this->_db->insert('users_session', array(
						'user_id' => $this->data()->id,
						'hash' => $hash
					));
				} else {
					$hash = $hashCheck->first()->hash;
				}
				Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
			}
			return true;
		}
		return false;
	}

	public function login_fb($ldap_name = null, $remember = true) {
		$user = $this->find(false, $ldap_name);
		if($user) {
			Session::put($this->_sessionName, $this->data()->id);
			if($remember) {
				$hash = Hash::unique();
				$hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));

				if(!$hashCheck->count()) {
					$this->_db->insert('users_session', array(
						'user_id' => $this->data()->id,
						'hash' => $hash
					));
				} else {
					$hash = $hashCheck->first()->hash;
				}
				Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
			}
			return true;
		}
		return false;
	}

	public function exists() {
		return (!empty($this->_data)) ? true : false;
	}

	public function isLoggedIn() {
		return $this->_isLoggedIn;
	}

	public function isAdmin() {
		if ($this->_isLoggedIn && $this->_data->group_id == 2) {
			return true;
		}
		return false;
	}

	public function isComplete() {
		if (!empty($this->_data->email) && !empty($this->_data->name)) {
			return true;
		}
		return false;
	}

	public function authorizeNotification($check_setting) {
		if (!empty($check_setting) && !empty($this->data()->settings)) {
			$settings = json_decode($this->data()->settings, true);
			if ($settings[$check_setting]) {
				return true;
			}
		}
		return false;
	}

	public function send_email($subject, $message) {
		Email::send($this->_data->email, $this->_data->name, Config::get('env/email'), $subject, $message);
	}

	public function logout() {
		$this->_db->delete('users_session', array('user_id', '=', $this->data()->id));
		Session::delete($this->_sessionName);
		Cookie::delete($this->_cookieName);
	}

	public function data() {
		return $this->_data;
	}

}