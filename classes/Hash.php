<?php

// generate varieties of hashes
/*
 * salt =	improves password security by adding a randomly generated secure string
 * 			of data onto the end of a password
 * 
 * By adding salt to the password it creates a unique hash for each password even if
 * the passwords themselves are the same!
 * 
 * =================================================
 * http://php.net/manual/en/faq.passwords.php
 * =================================================
 * "By applying a hashing algorithm to your user's passwords before storing them 
 * in your database, you make it implausible for any attacker to determine the 
 * original password, while still being able to compare the resulting hash to the 
 * original password in the future."
 * 
 * "It is important to note, however, that hashing passwords only protects them from 
 * being compromised in your data store, but does not necessarily protect them from 
 * being intercepted by malicious code injected into your application itself."
 */

class Hash {
	public static function make($string, $salt = '') {
		return hash('sha256', $string . $salt);
	}
	
	public static function salt($length) {
		return mcrypt_create_iv($length);
	}
	
	public static function unique() {
		return self::make(uniqid());
	}
}//end class