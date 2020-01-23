<?php 	

function sanitizePassword($inputText) {
	$inputText = strip_tags($inputText); 
	return $inputText;
}

function sanitizeFormUsername($inputText) {
	$inputText = strip_tags($inputText); // removes html tags from input field
	$inputText = str_replace(" ","", $inputText); // removes spaces from string
	return $inputText;
}

function sanitizeFormString($inputText) {
	$inputText = strip_tags($inputText); 
	$inputText = str_replace(" ","", $inputText); 
	// makes first character capital + strtolower makes every character to lower cases
	$inputText = ucfirst(strtolower($inputText));
	return $inputText;
}



if(isset($_POST['registerButton'])){
	// Register Button
	$username = sanitizeFormUsername($_POST['username']);
	//echo $username;
	$firstName = sanitizeFormString($_POST['firstName']);
	$lastName = sanitizeFormString($_POST['lastName']);
	$email = sanitizeFormString($_POST['email']);
	$email2 = sanitizeFormString($_POST['email2']);
	$password = sanitizePassword($_POST['password']);
	$password2 = sanitizePassword($_POST['password2']);

	$wasSuccessful = $account->register($username, $firstName, $lastName, $email, $email2, $password, $password2);
	if($wasSuccessful){
		$_SESSION['userLoggedIn'] = $username;
		header("Location: index.php"); // takes you to whatever page you write in ("")
	}
}


?>