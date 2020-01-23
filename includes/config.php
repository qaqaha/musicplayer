<?php 
	ob_start();

	// keeps logged in
	session_start();

	$timezone = date_default_timezone_set("Europe/London");
	
	// connection, user, password, database name
	$con = mysqli_connect("localhost", "root1", "1234", "slotify");

	if(mysqli_connect_errno()){
		echo "Failed to connect: " . mysqli_connect_errno();
	}

?>