<?php

// lets us access our config global variable in easy ways

class Config {
	public static function get($path = null) {
		if ($path) {
			$config = $GLOBALS['config'];			
			$path = explode('/', $path);// break up path by '/' character, return segments in an array	
			
			//loop through each segment
			foreach ($path as $bit) {
				//check if segment is set in the config variable
				if (isset($config[$bit])) {
					//if it exists, save that bit
					$config = $config[$bit];
				}
			}//end foreach		
			
			return $config;
		}//end if
		
		return false;
	}//end function
}//end class