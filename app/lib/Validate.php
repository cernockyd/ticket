<?php
namespace App\Lib;

class Validate {
	private $_passed = false,
			$_errors = array(),
			$_db = null;


	private $required_messages = [
		'gender' => 'pohlaví je povinné',
		'password' => 'heslo je povinné',
		'email' => 'email je povinný',
		'subject' => 'vyplňte předmět',
		'name' => 'jméno je povinné',
		'new_password' => 'zadejte nové heslo',
	];

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function escape( $string ) {
		return htmlentities( $string, ENT_QUOTES, 'UTF-8' );
	}

	public function check($source, $items = array()) {
		$test_special = false;

		foreach($items as $item => $rules) {
			foreach($rules as $rule => $rule_value) {

				$value = (!empty($source[$item])) ? trim($source[$item]) : null ;
				$item = $this->escape($item);

				if ($rule === 'required' && $rule_value && $value === null) {

					if (array_key_exists($item, $this->required_messages)) {
						$e = $this->required_messages[$item];
					} else {
						$e = "položka {$item} je povinná";
					}

				} else if($value !== null) {
					switch ($rule) {
						case 'min':
							if(strlen($value) < $rule_value) {
								if ($item == 'password') { $i = 'heslo'; } else { $i = $item; }
								$e = "{$i} musí mít minimálně {$rule_value} znaků";
							}
						break;
						case 'max':
							if(strlen($value) > $rule_value) {
								if ($item == 'password') { $item = 'heslo'; }
								$e = "{$item} musí mít minimálně {$rule_value} znaků";
							}
						break;
						case 'equals':
							if(strlen($value) != $rule_value) {
								if ($item == 'password') { $item = 'heslo'; }
								$e = "{$item} musí být dlouhé {$rule_value} znaků";
							}
						break;
						case 'matches':
							if($value != $source[$rule_value]) {
								$e = "{$rule_value} musí být {$item}";
							}
						break;
						case 'unique':
							$check = $this->_db->get($rule_value, array($item, '=', $value));
							if ($check->count()) {
								$e = "{$item} již existuje.";
							}
						break;
						case 'email':
							if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
								$e = "Zkontrolujte prosím email.";
							}
						break;
						case 'boolean':
							if (!is_bool($value)) {
								$e = ":(";
							}
						break;
						case 'integer':
							if (!is_numeric($value)) {
								$e = ":(";
							}
						break;
						case 'taxonomy':
							$data = $this->_db->get('taxonomies', ['id', '=', $value]);
							if (!$data->count()) {
								$e = "Kategorie neexistuje.";
							}
						break;
						case 'test':
							$test_special = true;
						break;
					}
				}
			}
			if (!empty($e)) {
				$this->addError($item, $e);
			}
		}

		if(empty($this->_errors)) {
			if (!$test_special) {
				$this->_passed = true;
			}
		}

		return $this;
	}

	public function validate_media() {
		if (Session::exists('AddProduct/Media')) {
      $media = Session::get('AddProduct/Media');
    	$count = count($media);
    	if ($count < 1) {
    		$this->addError('media', 'Musíte nahrát nejméně jednu fotku.');
    	} elseif (!$count > 3) {
    		$this->addError('media', 'Můžete nahrát maximálně 3 fotky.');
    	}
    } else {
    	$this->addError('media', 'Musíte nahrát nejméně jednu fotku.');
    }
	}

	private function addError($name, $error) {
		$this->_errors[] = [$name => $error];
	}

	public function valid_phone($phone) {
		return ( ! preg_match_all("/^\s*(?:\+?(\d{1,3}))?([-. (]*(\d{3})[-. )]*)?((\d{3})[-. ]*(\d{2,4})(?:[-.x ]*(\d+))?)\s*$/m", $phone)) ? FALSE : TRUE;
  }


	public function errors() {
		return $this->_errors;
	}

	public function passed() {
		return $this->_passed;
	}

}