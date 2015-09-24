<?php

/*
 * Works with register.php and sanitize.php to handle all validation
 * for user input.
 * 
 */

class Validate {
	private $_passed = false,
			$_errors = array(),
			$_db = null;
	
	public function __construct() {
		//connect to the database
		$this->_db = DB::getInstance();		
	}
	
	/*
	 * $source = either $_POST or $_GET
	 * $items = an array of 'rules' to use to help validate user input.
	 * 	Each item in the array will be an array itself.
	 * 	$items/item = username, password, password_again, and name
	 * 	$rules/rule = required, min, max, unique, etc...
	 * 	$rule_value = true, 2, 20, users, etc...
	 * 
	 * 'username' => array(
			'required' => true,
			'min' => 2,
			'max' => 20,
			'unique' => 'users'
		),
		'password' => array(
			'required' => true,
			'min' => 6,			
		),
		'password_again' => array(
			'required' => true,
			'matches' => 'password'
		),
		'name' => array(
			'required' => true,
			'min' => 2,
			'max' => 50,			
		)
	 */
	public function check($source, $items = array()) {
		foreach ($items as $item => $rules) {
			foreach ($rules as $rule => $rule_value) {
				
				$value = trim($source[$item]);//get user input data
				$item = escape($item);//method from sanitize.php
				
				//if the rule is required but the value is empty
				if ($rule === 'required' && empty($value)) {
					$this->addError("{$item} is required");//add an error message to the array
				} else if (!empty($value)) {//otherwise if there is a value
					switch($rule) {
						case 'min':
							if (strlen($value) < $rule_value) {
								$this->addError("{$item} must be a minimum of {$rule_value} characters.");
							}
							break;
							
						case 'max':
							if (strlen($value) > $rule_value) {
								$this->addError("{$item} must be a maximum of {$rule_value} characters.");
							}
							break;
							
						case 'matches':
							if ($value != $source[$rule_value]) {
								$this->addError("{$rule_value} must match {$item}");
							}
							break;
							
						case 'unique':
							$check = $this->_db->get($rule_value, array($item, '=', $value));
							if ($check->count()) {
								$this->addError("{$item} already exists.");
							}
							break;														
					}//end switch
				}//end if
			}//end foreach
		}//end foreach
		
		if (empty($this->_errors)) {//if there are no errors
			$this->_passed = true;//data has passed validation
		}
		
		return $this;//return the current working object
	}//end function
	
	private function addError($error) {
		$this->_errors[] = $error;
	}
	
	public function errors() {
		return $this->_errors;
	}
	
	public function passed() {
		return $this->_passed;
	}
}//end class