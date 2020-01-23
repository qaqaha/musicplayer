<?php 
include("../../config.php"); 

if(!isset($_POST['username'])){
	echo "ERROR: could not set username.";
	exit();
}

if(isset($_POST['email']) && $_POST['email'] != ""){
	$username = $_POST['username'];
	$email = $_POST['email'];

	// check if email is valid format
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		echo "Email is invalid";
		exit();
	}

	// check if email in use, last part checks if its the same already in use email adress
	$emailCheck = mysqli_query($con, "SELECT email FROM users WHERE email ='$email' AND username != '$username'");
	if(mysqli_num_rows($emailCheck) > 0){
		echo "Email is already in use.";
		exit();
	}

	$updateQuery = mysqli_query($con, "UPDATE users SET email = '$email'  WHERE username = '$username' ");
	echo "Update successful";

} else {
	echo "You must provide a email adress.";
}

?>