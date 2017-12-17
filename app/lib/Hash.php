<?php
namespace App\Lib;
class Hash {

	public static function make($string, $salt = '') {
		return hash('sha256', $string . $salt);
	}

	public static function salt($length) {
		return utf8_encode(random_bytes($length));
	}

	public static function unique() {
		return self::make(uniqid());
	}

}