<?php 
include("includes/includedFiles.php");

if(isset($_GET['term'])){
	// decodes "%20" in url bar to space
	$term = urldecode($_GET['term']);
} else {
	$term = "";
}

?>

<div class="searchContainer">

	<h4>Search for an artist, album or song</h4>
	<input type="text" class="searchInput" value="<?php echo $term; ?>" placeholder="Type here..." onfocus="var val= this.value; this.value=''; this.value= val;">

</div>

<script>

	// everytime the page loads it gives input field focus
	
	$(".searchInput").focus();

	// search bar 2 seconds reload page function
	$(function(){
		// when typing, 2 secs later page refreshes
		
		
		$(".searchInput").keyup(function(){
			clearTimeout(timer);

			// and this is reseting the new one.
			timer = setTimeout(function(){
				var val = $(".searchInput").val(); 
				openPage("search.php?term=" + val); 
			}, 2000);
		})

	})
</script>


<?php 
// prevents showing on no search every item in songs, albums and artists

if($term == "") exit();
?>

<div class="trackListContainer borderBottom">
	<h2>Songs</h2>
	<ul class="trackList">

	<?php 

		// %= anything after.
		$songQuerry = mysqli_query($con, "SELECT id FROM songs WHERE title LIKE '$term%'");

		// getting songs from search result
		if(mysqli_num_rows($songQuerry) == 0){
			echo "<span class='noResults'>No songs found matching " . $term . "</span>";
		}

		// playlist
		$songIdArray = array();

		$i = 1;
		while($row = mysqli_fetch_array($songQuerry)) {

			if($i > 15){
				break;
			}
			
			array_push($songIdArray, $row['id']);

			$albumSong = new Song($con, $row['id']);
			$albumArtist = $albumSong->getArtist();

			echo "<li class='trackListRow'>
					<div class='trackCount'>
						<img class='play' src='assets/images/icons/play-white.png' onclick='setTrack(\"" . $albumSong->getId() . "\"
						, tempPlayList, true)'>
						<span class='trackNumber'>$i</span>
					</div>

					<div class='trackInfo'>
						<span class='trackName'>" . $albumSong->getTitle() . "</span>
						<span class='artistName'>" . $albumArtist->getName() . "</span>
					</div>

					<div class='trackOptions'>
						<input type='hidden' class='songId' value='" . $albumSong->getId() . "'</input>
						<img class='optionsButton' src='assets/images/icons/more.png' onclick='showOptionsMenu(this)'>
					</div>

					<div class='trackDuration'>
						<span class='duration'>" . $albumSong->getDuration() . "</span>
					</div>

				</li>";

			$i++;

		}
	?>

	<script>
		var tempSongIds = '<?php echo json_encode($songIdArray); ?>';
		tempPlayList = JSON.parse(tempSongIds); 
	</script>
		



	</ul>
</div>

<div class="artistsContainer borderBottom">
	<h2>Artists</h2>
	<?php 

		$artistQuery = mysqli_query($con, "SELECT id FROM artists WHERE name LIKE '$term%' LIMIT 10");

		// getting artists from search result
		if(mysqli_num_rows($artistQuery) == 0){
			echo "<span class='noResults'>No artists found matching " . $term . "</span>";
		}

		while($row = mysqli_fetch_array($artistQuery)){
			$artistFound = new Artist($con, $row['id']);

			echo "<div class='searchResultRow'>
				<div class='artistName'>

					<span role'link' tabindex='0' onclick='openPage(\"artist.php?id=" . $artistFound->getId() ."\")'>
						"
						. $artistFound->getName() .
					"
					</span>

				</div>	
			</div>";
		}

	?>
</div>		

<div class="gridViewContainer">

	<h2>Albums</h2>

	<?php  
		$albumQuery = mysqli_query($con, "SELECT * FROM albums WHERE title LIKE '$term%' LIMIT 10");

		if(mysqli_num_rows($albumQuery) == 0){
			echo "<span class='noResults'>No albums found matching " . $term . "</span>";
		}

		while($row = mysqli_fetch_array($albumQuery)){

			echo "<div class='gridViewItem'>
					<span role='link' tabindex='0' onclick='openPage(\"album.php?id=" . $row['id'] . "\")'>
						<img src='" . $row['artworkPath'] . "'>

						<div class='gridViewInfo'>"
							. $row['title'] .
						"</div>
					</span>
				</div>";
		}
	?>
</div>

<nav class="optionsMenu">
	<input type="hidden" class="songId">
	<?php echo Playlist::getPlaylistsDropdown($con, $userLoggedIn->getUsername()); ?>
	<div class="item">Share on facebook</div>
</nav>