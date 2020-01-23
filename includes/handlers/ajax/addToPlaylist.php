<?php 
include("../../config.php");

if(isset($_POST['playlistId']) && isset($_POST['songId'])){
	$playlistId = $_POST['playlistId'];
	$songId = $_POST['songId'];

	// returns the highest playlistOrder so the new song will be added to playlist as last song
	$orderIdQuery = mysqli_query($con, "SELECT MAX(plOrder) + 1 as plOrder FROM plsongs WHERE plId='$playlistId'");
	

	$row = mysqli_fetch_array($orderIdQuery);
	$order = $row['plOrder'];

	$query = mysqli_query($con, "INSERT INTO plsongs VALUES(NULL, '$songId', '$playlistId', '$order')");
	

} else {
	echo "playlistId or songId waas not passed to addToPlaylist.php";
}

?>