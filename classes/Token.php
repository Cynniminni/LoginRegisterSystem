<?php

/*
 * In the Input class it could allow for Cross Site Request Forgery (CSRF), a security risk.
 * CSRF is basically when an attacker 'forges' a request from a legitimate user in order
 * to steal information or do other things, etc.
 * 
 * Ex: creating a fake link that sends a request to logout through
 * GET method (harmless, but an example)
 * 
 * To prevent this, we can generate a token (a unique string tied to a specific action) that
 * can be validated before the action is processed.
 * 
 * Token.php will generate a token and then check if a token is valid/exists and then deletes
 * that token. Each refresh of a page will have a newly generated token that that page only knows
 * so another user somewhere else can't steal that page.
 */

class Token {
	//static method to generate a token
	//'session/token_name' is just 'token'
	public static function generate() {
		return Session::put(Config::get('session/token_name'), md5(uniqid()));
	}//end function
	
	//the $token here is from the register.php form
	public static function check($token) {
		$tokenName = Config::get('session/token_name');//'token'
		
		if (Session::exists($tokenName) && $token === Session::get($tokenName)) {
			Session::delete($tokenName);
			return true;
		}
		
		return false;
	}//end function
}//end class