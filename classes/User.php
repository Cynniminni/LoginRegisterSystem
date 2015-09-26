<?php

class User {
	private $_db;//the db instance
	private $_data;//holds user data from db: id, username, password, etc.
	private $_sessionName;//hold user session
	private $_isLoggedIn;
	private $_cookieName;
	
	public function __construct($user = NULL) {
		//get db instance
		$this->_db = DB::getInstance();
		
		//get session name, which is just 'user'. See init.php
		$this->_sessionName = Config::get('session/session_name');
		$this->_cookieName = Config::get('remember/cookie_name');
		
		if (!$user) {//if a user has NOT been defined
			if (Session::exists($this->_sessionName)) {
				$user = Session::get($this->_sessionName);

				//check if user exists
				if ($this->find($user)) {
					//if there is a session and the user exists,
					//signify they're logged in
					$this->_isLoggedIn = true;
					
				} else {
					//process logout
				}
			}
		} else {//if a user HAS been defined
			$this->find($user);
		}//end if
	}//end function
	
	public function create($fields = array()) {
		//insert a new user
		if (!$this->_db->insert('users', $fields)) {
			throw new Exception('There was a problem creating an account.');
		}
	}
	
	public function find($user = NULL) {
		if ($user) {
			//check if we're finding user by id or username
			$field = (is_numeric($user)) ? 'id' : 'username';
			
			//try to query database 
			$data = $this->_db->get('users', array($field, '=', $user));
			
			if ($data->count()) {//if user exists
				$this->_data = $data->first();//store them
				return true;
			}
		}
	}
	
	/**
	 * Log in a user by creating a session for that user.
	 * @param string $username
	 * @param string $password
	 * @param unknown $remember
	 * @return boolean
	 */
	public function login($username = NULL, $password = NULL, $remember = false) {			
		if (!$username && !$password && $this->exists()) {
			//automatically log them in
			Session::put($this->_sessionName, $this->data()->id);
		} else {
			$user = $this->find($username);			
			
			if ($user) {			
				//if db password matches inputted password, using same salt to check
				if ($this->data()->password === Hash::make($password, $this->data()->salt)) {
					//log in the user by creating a session
					//$_SESSION['user'] = user's id
					Session::put($this->_sessionName, $this->data()->id);				
					
					if ($remember) {//user wants to be remembered
						$hash = Hash::unique();//create unique hash	
										
						//check if we already have a hash stored for them in the db
						$hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));
						
						if (!$hashCheck->count()) {												
							//if there is no hash, insert one
							$this->_db->insert('users_session', array(
								'user_id' => $this->data()->id,
								'hash' => $hash
							));
						} else {						
							//get the hash
							$hash = $hashCheck->first()->hash;
						}
											
						//make a cookie for remember me
						//_cookieName = 'hash', see init.php
						Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));									
					}//end if
					
					//signify login is successful
					return true;
				}//end if
			}//end if								
		}//end outer if
				
		//signify login has failed
		return false;
	}//end function
	
	public function exists() {
		return (!empty($this->_data)) ? true : false;
	}
	
	public function logout() {
		//log out the user by deleting their session
		Session::delete($this->_sessionName);
		
		//delete their cookie
		Cookie::delete($this->_cookieName);
		
		//delete the cookie in the db
		$this->_db->delete('users_session', array(
			'user_id', '=', $this->data()->id
		));
	}
	
	public function data() {
		return $this->_data;
	}
	
	public function isLoggedIn() {
		return $this->_isLoggedIn;
	}
}//end class