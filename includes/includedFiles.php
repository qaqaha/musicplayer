<?php 
	// checks if request was by ajax or manually
	// every ajax request contains this: HTTP_X_REQUESTED_WITH
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {

		// if set by ajax, dont change header and footer
		include("includes/config.php");
		include("includes/classes/User.php");
		include("includes/classes/Artist.php");
		include("includes/classes/Album.php");
		include("includes/classes/Song.php");
		include("includes/classes/Playlist.php");

		if(isset($_GET['userLoggedIn'])){

			// page can now access the userLoggedIn obj.
			$userLoggedIn = new User($con, $_GET['userLoggedIn']);
		} else {
			echo "Username variable was not passed into the page. Check the openPage JS function";
			exit(); // dont load page
		}


	} else {

		// if typed manually we want to change header and footer
		include("includes/header.php");
		include("includes/footer.php");

		$url = $_SERVER['REQUEST_URI'];
		echo "<script>openPage('$url')</script>";
		exit();

	}
?>