<?php 

/*
 * This file works with Input.php and Validate.php to take in user
 * input data and then register them.
 */

require_once 'core/init.php';

if (Input::exists()) {//if data has been submitted
	$validate = new Validate();
	
	//check returns the current Validate object
	$validation = $validate->check($_POST, array(
		'username' => array(
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
	));
	
	if ($validation->passed()) {
		// register user
		echo 'Passed';
	} else {
		//print errors
		foreach ($validation->errors() as $error) {
			echo $error, '<br>';
		}
	}//end if
}//end if

?>

<!-- for="username" tells the label what input it's for through the input's id, "username" -->
<!-- action="" sends data to the same page (register.php) -->
<form action="" method="post">
	<div class="field">
		<label for="username">Username</label>
		<input type="text" name="username" id="username" value="<?php echo escape(Input::get('username'));?>" autocomplete="off">			
	</div>
	
	<div class="field">
		<label for="password">Choose a password</label>
		<input type="password" name="password" id="password">
	</div>
	
	<div class="field">
		<label for="password_again">Enter your password again</label>
		<input type="password" name="password_again" id="password_again">
	</div>
	
	<div class="field">
		<label for="name">Your Name</label>
		<input type="text" name="name" id="name" value="<?php echo escape(Input::get('name'));?>">
	</div>
	
	<input type="submit" value="Register">
</form>