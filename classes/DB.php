<?php

// database wrapper, work with PDO to connect to MySQL database

class DB {
	
	//don't have to connect to db repeatedly using getInstance method
	private static $_instance = null;
	
	private $_pdo, 
			$_query, 
			$_error = false, 
			$_results, 
			$_count = 0;
	
	private function __construct() {
		try {
			// establish connection to database
			$this->_pdo = new PDO(
				'mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), 
				Config::get('mysql/username'), 
				Config::get('mysql/password')
			);
		} catch (PDOException $e) {
			echo 'Not connected. </br>';			
			// if connection fails, kill the application
			// and output the error message
			die($e->getMessage());
		}
	}	
	
	public static function getInstance() {			
		// if class hasn't been instantiated yet
		if (!isset(self::$_instance)) {
			// instantiate it
			self::$_instance = new DB();
		}
		
		// return the instance of itself
		return self::$_instance;
	}
	
	public function query($sql, $params = array()) {
		// reset error so that we're not returning an error from a previous query
		$this->_error = false;
		
		// check if query is prepared successfully
		// if prepare() fails it will return false, this is how we check it
		if ($this->_query = $this->_pdo->prepare($sql)) {			
			$x = 1;
			
			// check if there's anything in the array
			if (count($params)) {
				// bind each value to each placeholder in the statement
				foreach ($params as $param) {
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}
			
			if ($this->_query->execute()) {				
				// save the resultset using fetchAll
				// PDO::FETCH_OBJ: returns an anonymous object with property names that 
				// correspond to the column names returned in your result set
				$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount();
			} else {
				$this->_error = true;				
			}
		}//end if
		
		// return current object we're working on
		return $this;
	}//end function
	
	
	public function action($action, $table, $where = array()) {
		// need a field, an operator, and a value in the where clause
		if (count($where) === 3) {
			$operators = array(
					'=', 
					'>', 
					'<', 
					'>=',
					'<='
			);
			
			$field = $where[0];
			$operator = $where[1];
			$value = $where[2];
			
			// if operator is a member of operators
			if (in_array($operator, $operators)) {
				$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
				
				if (!$this->query($sql, array($value))->error()) {//get error here
					return $this;
				}
			}
		}//end if
		
		return false;
	}//end function
	
	public function get($table, $where) {
		return $this->action('SELECT *', $table, $where);
	}
	
	public function delete($table, $where) {
		return $this->action('DELETE', $table, $where);
	}
	
	/**
	 * Insert a row into a table in the 'login' database.
	 * 
	 * @param unknown $table Name of a database table.
	 * @param unknown $fields Columns of the table.
	 * @return boolean A boolean representing whether the insertion was successful.
	 */
	public function insert($table, $fields = array()) {		
		$keys = array_keys($fields);
		$values = null;
		$x = 1;
		
		foreach($fields as $field) {
			$values .= '?';
			
			//if we're not at the end of our values, add commas
			if ($x < count($fields)) {
				$values .= ', ';
			}
			$x++;
		}
		
		// join the keys together with `,` in between them
		$sql = "INSERT INTO {$table} (`" . implode('`,`', $keys) ."`) 
				VALUES ($values)";
		
		if (!$this->query($sql, $fields)->error()) {
			echo $sql;
			return true;
		}
				
		return false;
	}
	
	public function update($table, $id, $fields) {
		$set = '';
		$x = 1;
		
		foreach ($fields as $name => $value) {
			$set .= "{$name} = ?";
			
			if ($x < count($fields)) {
				$set .= ', ';
			}
			
			$x++;
		}		
		
		$sql = "UPDATE {$table} SET {$set} 
				WHERE id = {$id}";
		
		if (!$this->query($sql, $fields)->error()) {
			return true;
		}
		
		return false;
	}
	
	public function results() {
		return $this->_results;
	}
	
	public function first() {
		return $this->results()[0];// get only first result from the query
	}
	
	public function error() {
		return $this->_error;
	}
	
	public function count() {
		return $this->_count;
	}
}//end class