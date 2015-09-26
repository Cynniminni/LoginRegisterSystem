<?php

/*
 * Session.php helps set and unset token values for $_SESSION['token'],
 * and lets us check if it exists, as well as getting and deleting it.
 * This helps check user authenticity by preventing CSRF.
 * 
 * In order to use the $_SESSION variable, we have to start a session 
 * with session_start(). This is done in init.php.
 * 
 * Logging in a user is creating a new session.
 * Logging out a user is deleting the existing session.
 */

class Session {
	//$name = session name
	//$value = ?
	//sets $_SESSION['token'] equal to a unique string identifier, which is out token
	public static function put($name, $value) {
		return $_SESSION[$name] = $value;
	}//end function
	
	public static function exists($name) {
		return (isset($_SESSION[$name])) ? true : false;
	}//end function
	
	public static function delete($name) {
		if (self::exists($name)) {//call static method within itself
			unset($_SESSION[$name]);//delete that session token
		}
	}
	
	public static function get($name) {
		return $_SESSION[$name];//get the token string
	}
	
	//$name = name of flash message
	//$string = content of flash message
	//show a message to the user once
	public static function flash($name, $string = '') {
		if (self::exists($name)) {
			$session = self::get($name);//get the session (token)
			self::delete($name);
			
			return $session;
		} else {
			self::put($name, $string);//make a new token?
		}				
	}
}//end class