<?php

// going to be included on every page we use
// allows us to autoload classes

session_start();// allow people to login

$GLOBALS['config'] = array(			// put settings in global var
	'mysql' => array(
		'host' => 'localhost',		// 127.0.0.1 in tutorial
		'username' => 'root',
		'password' => '',
		'db' => 'login'
	),
	'remember' => array(
		'cookie_name' => 'hash',
		'cookie_expiry' => 604800	// how long users will be remembered
	),
	'session' => array(
		'session_name' => 'user'
	)
);

// autoload required classes as we need them
spl_autoload_register(function($class) {
	require_once 'classes/' . $class . '.php';
});

require_once 'functions/sanitize.php';