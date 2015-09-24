<?php

/*
 * This class works with register.php to check if any data has been
 * submitted to $_POST or $_GET and fetches the data accordingly.
 */

class Input {
	public static function exists($type = 'post') {
		switch($type) {
			//check if $_POST has any data
			case 'post':
				return (!empty($_POST)) ? true : false;
				break;
			
			//check if $_GET has any data
			case 'get':
				return (!empty($_GET)) ? true : false;
				break;
				
			//no data found
			default:
				return false;
				break;
		}
	}//end function
	
	public static function get($item) {
		//get data from $_POST/$_GET using $item as an index
		if (isset($_POST[$item])) {
			return $_POST[$item];
		} else if (isset($_GET[$item])) {
			return $_GET[$item];
		}
		
		return '';
	}//end function
}//end class