<?php 
include("../../config.php");

if(isset($_POST['playlistId'])){
	$playlistId = $_POST['playlistId'];
	$playlistQuery = mysqli_query($con, "DELETE FROM pl WHERE id='$playlistId'");
	$songsQuery = mysqli_query($con, "DELETE FROM plsongs WHERE plId='$playlistId'");

} else {
	echo "PlaylistId was not passed into deletePlaylist.php";
}

?>