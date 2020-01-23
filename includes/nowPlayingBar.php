<?php 

$songQuery = mysqli_query($con, "SELECT id FROM songs ORDER BY RAND() LIMIT 10" );

$resultArray = array();
while($row = mysqli_fetch_array($songQuery)) {
	array_push($resultArray, $row['id']);
}

$jsonArray = json_encode($resultArray);

?>

<script>

$(document).ready(function(){
	//currentPlayList
	var newPlayList = <?php echo $jsonArray; ?>;
	audioElement = new Audio();
	setTrack(newPlayList[0], newPlayList, false);
	updateVolumeProgressBar(audioElement.audio);

	// prevent highlighting in nowPlayingBar
	$("#nowPlayingBarContainer").on("mousedown touchstart mousemove touchmove", function(e){
		e.preventDefault();
	});

	// progressbar of track
	$(".playbackBar .progressBar").mousedown(function(){
		mouseDown = true;
	});

	
	$(".playbackBar .progressBar").mousemove(function(e){
		if(mouseDown){
			// set time of song depending on position of mouse
			// calc percentage of track where clicked
			timeFromOffset(e, this);

		}
	});

	$(".playbackBar .progressBar").mouseup(function(e){
		timeFromOffset(e, this);
	});


	// volumebar of track
	$(".volumeBar .progressBar").mousedown(function(){
		mouseDown = true;
	});
	
	$(".volumeBar .progressBar").mousemove(function(e){
		if(mouseDown){

			var percentage = e.offsetX / $(this).width();

			if(percentage >= 0 && percentage <= 1){
				// if between 0 and 1, set volume to that
				audioElement.audio.volume = percentage;
			}
			
		}
	});

	$(".volumeBar .progressBar").mouseup(function(e){
		var percentage = e.offsetX / $(this).width();

		if(percentage >= 0 && percentage <= 1){
			
			audioElement.audio.volume = percentage;
		}
	});


	// whereever we let down the mouse -> if not its gonna always drag the progressbar.
	$(document).mouseup(function(){
		mouseDown = false;
	});


});

// progressbar of track drag and click the track duration
function timeFromOffset(mouse, progressBar){
	// mouse position on bar
	var percentage = mouse.offsetX / $(progressBar).width() * 100; 
	// setting the track
	var seconds = audioElement.audio.duration * (percentage / 100);
	audioElement.setTime(seconds);
}

// previous song function
function prevSong(){
	if(audioElement.audio.currentTime >= 3 || currentIndex == 0){
		// common usage in audioplayers to put first the track back to the beginning and if pressed again
		// it goes to the previous song.
		audioElement.setTime(0);
	} else {
		currentIndex = currentIndex - 1;
		setTrack(currentPlayList[currentIndex], currentPlayList, true);
	}
}

// next song function
function nextSong(){

	// repeat song
	if(repeat == true){
		// sets time back of track to 0 -> repeat song
		audioElement.setTime(0);
		playSong();
		return;
	}

	// play next song
	if(currentIndex == currentPlayList.length -1){
		currentIndex = 0;
	} else {
		currentIndex++;
	}

	// shuffle true: play shuffleplaylist, if false, currenplaylist
	var trackToPlay = shuffle ? shufflePlayList[currentIndex] : currentPlayList[currentIndex]; // contains the track id to play
	setTrack(trackToPlay, currentPlayList, true);
};

// set repeat function
function setRepeat(){
	repeat = !repeat;
	
	var imageName = repeat ? "repeat-active.png" : "repeat.png";
	$(".controlButton.repeat img").attr("src", "assets/images/icons/" + imageName);
}

// set mute volume
function setMute(){
	audioElement.audio.muted = !audioElement.audio.muted;
	var imageName = audioElement.audio.muted ? "volume-mute.png" : "volume.png";
	$(".controlButton.volume img").attr("src", "assets/images/icons/" + imageName);
}

// shuffle function
function setShuffle(){
	shuffle = !shuffle;
	var imageName = shuffle ? "shuffle-active.png" : "shuffle.png";
	$(".controlButton.shuffle img").attr("src", "assets/images/icons/" + imageName);

	// TEST
	console.log("Current :" + currentPlayList);
	console.log("Shuffle :" + shufflePlayList);
	// TEST

	if(shuffle == true){
		// randomize
		// shufflePlayList is a copy of currentplaylist
		shuffleArray(shufflePlayList);
		// preventing on shuffle to repeat the same song
		currentIndex = shufflePlayList.indexOf(audioElement.currentlyPlaying.id);

	} else {
		// randomize deactivated, back to normal playlist
		// if deactivated we set the cur index to be wherever the song is in the ordered playlist
		currentIndex = currentPlayList.indexOf(audioElement.currentlyPlaying.id);

	}
}

// shuffle array
function shuffleArray(a) {
    var j, x, i;
    for (i = a.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = a[i];
        a[i] = a[j];
        a[j] = x;
    }
    return a;
}


// setting track and playlist
function setTrack(trackId, newPlaylist, play){

	// duplicating playlist for shuffle
	if(newPlaylist != currentPlayList){
		// both are exactly the same
		currentPlayList = newPlaylist;
		// return copy of the array which doesnt affect the currentPlayList
		shufflePlayList = currentPlayList.slice(); 
		// make it shuffle:
		shuffleArray(shufflePlayList);
	}

	if(shuffle == true){
		currentIndex = shufflePlayList.indexOf(trackId);
	} else {
		// setting current song id into currentIndex variable
		currentIndex = currentPlayList.indexOf(trackId);
	}
	
	pauseSong();

	
	$.post("includes/handlers/ajax/getSongJson.php", { songId: trackId }, function(data){

		var track = JSON.parse(data); 

		$(".trackName span").text(track.title);

		$.post("includes/handlers/ajax/getArtistJson.php", { artistId: track.artist }, function(data){
			var artist = JSON.parse(data);

			$(".trackInfo .artistName span").text(artist.name);
			// links us to the artist on click
			$(".trackInfo .artistName span").attr("onclick", "openPage('artist.php?id=" + artist.id + "')");
		});

		$.post("includes/handlers/ajax/getAlbumJson.php", { albumId: track.album }, function(data){
			var album = JSON.parse(data);

			$(".content .albumLink img").attr("src", album.artworkPath);
			// links us to the album on click
			$(".content .albumLink img").attr("onclick", "openPage('album.php?id=" + album.id + "')");
			$(".trackInfo .trackName span").attr("onclick", "openPage('album.php?id=" + album.id + "')");
		});

		
		audioElement.setTrack(track);

		if(play){
			
			playSong();
		}
		

		

	});


}

function playSong() {

	if(audioElement.audio.currentTime == 0) {
		
		$.post("includes/handlers/ajax/updatePlays.php", { songId: audioElement.currentlyPlaying.id});
	} else {
		
	}

	$(".controlButton.play").hide();
	$(".controlButton.pause").show();
	audioElement.play();
}

function pauseSong() {
	$(".controlButton.play").show();
	$(".controlButton.pause").hide();
	audioElement.pause();
}


</script>

<div id="nowPlayingBarContainer">
	<div id="nowPlayingBar">
		<div id="nowPlayingLeft">
			<div class="content">
				<span class="albumLink">
					<img role="link" tabindex="0" src="" class="albumArtwork">
				</span>
				<div class="trackInfo">
					<span class="trackName">
						<span role="link" tabindex="0"></span>
					</span>
					
					<span class="artistName">
						<span role="link" tabindex="0"></span>
					</span>
				</div>
			</div>
		</div>

		<div id="nowPlayingCenter">

			<div class="content playerControls">
				
				<div class="buttons">

					<button class="controlButton shuffle" title="Shuffle Button" onclick="setShuffle()">
						<img src="assets/images/icons/shuffle.png" alt="Shuffle">
					</button>

					<button class="controlButton previous" title="Previous Button" onclick="prevSong()">
						<img src="assets/images/icons/previous.png" alt="Previous">
					</button>

					<button class="controlButton play" title="Play Button" onclick="playSong()">
						<img src="assets/images/icons/play.png" alt="Play">
					</button>

					<button class="controlButton pause" title="Pause Button" style="display: none;" onclick="pauseSong()">
						<img src="assets/images/icons/pause.png" alt="Pause">
					</button>

					<button class="controlButton next" title="Next Button" onclick="nextSong()">
						<img src="assets/images/icons/next.png" alt="Next">
					</button>

					<button class="controlButton repeat" title="Repeat Button" onclick="setRepeat()">
						<img src="assets/images/icons/repeat.png" alt="Repeat">
					</button>

				</div>

				<div class="playbackBar">

					<span class="progressTime current">0.00</span>

					<div class="progressBar">
						<div class="progressBarBg">
							<div class="progress"></div>
						</div>
					</div>

					<span class="progressTime remaining">0.00</span>
					
				</div>

			</div>

		</div>

		<div id="nowPlayingRight">
			<div class="volumeBar">
				<button type="" class="controlButton volume" title="Volume Button" onclick="setMute()">
					<img src="assets/images/icons/volume.png" alt="volume">
				</button>

				<div class="progressBar">
					<div class="progressBarBg">
						<div class="progress"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>