<?php

require_once 'core/init.php';

$user = DB::getInstance()->update('users', 3, array(		
		'password' => 'newpassword',	
		'name' => 'Dale Garrett'	
));