<?php

class Cookie {
	public static function exists($name) {
		return (isset($_COOKIE[$name])) ? true : false;
	}
	
	public static function get($name) {
		return $_COOKIE[$name];
	}
	
	public static function put($name, $value, $expiry) {
		if (setcookie($name, $value, time() + $expiry, '/')) {
			return true;//signify successful cookie made
		}
		
		return false;//signify cookie could not be made
	}
	
	public static function delete($name) {
		//reset the cookie to delete it
		self::put($name, '', time() - 1);
	}
}