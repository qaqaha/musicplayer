var currentPlayList = [];
var shufflePlayList = [];
var tempPlayList = []; // contains songs of album page
var audioElement;
var mouseDown = false;
var currentIndex = 0;
var repeat = false;
var shuffle = false;
var userLoggedIn;
var timer;

// will hide options menu on clicking away
$(document).click(function(click){
	var target = $(click.target);

	// if the clickek target doesnt have the class item and optionsMenu
	if(!target.hasClass("item") && !target.hasClass("optionsButton") ){
		hideOptionsMenu();
	}
});

// will hide options menu on scrolling
$(window).scroll(function(){
	hideOptionsMenu();
});

// getting id from songs dropdown
$(document).on("change", "select.playlist", function(){
	var select = $(this);

	// this refers to the element on which is fired, in this case it contains $dropdown <option value=$id
	// so everytime it changes playlistId will contain that id
	var playlistId = select.val();

	var songId = select.prev(".songId").val();

	$.post("includes/handlers/ajax/addToPlaylist.php", { playlistId: playlistId, songId: songId })
	.done(function(error){

		if(error != ""){
			alert(error);
			return;
		}

		hideOptionsMenu();

		// for preventing to be the same playlist on next dropdown
		select.val("");
	});
});

// update email in user info
function updateEmail(emailClass){
	var emailValue = $("." + emailClass).val();

	$.post("includes/handlers/ajax/updateEmail.php", { email: emailValue, username: userLoggedIn })
	.done(function(response){
		$("." + emailClass).nextAll(".message").text(response);
	});
}


function updatePassword(oldPasswordClass, newPasswordClass1, newPasswordClass2) {
	var oldPassword = $("." + oldPasswordClass).val();
	var newPassword1 = $("." + newPasswordClass1).val();
	var newPassword2 = $("." + newPasswordClass2).val();

	$.post("includes/handlers/ajax/updatePassword.php", 
	{ oldPassword: oldPassword,
		newPassword1: newPassword1,
		newPassword2: newPassword2, 
		username: userLoggedIn})

	.done(function(response) {
		$("." + oldPasswordClass).nextAll(".message").text(response);
	})


}

// logout function
function logout(){
	$.post("includes/handlers/ajax/logout.php", function(){
		location.reload();
	});
}

// changing page while actually not loading again so song continue playing. we want to load only maincontainer
function openPage(url){

	// if you type in search bar something and leave the page, after 2 seconds it takes you back.
	// this will prevent this.
	if(timer != null){
		clearTimeout(timer);
	}

	// if no ? in the url, add one. example: album.php?id=5 to have ?
	if(url.indexOf("?") == -1){
		url = url + "?";
	}

	// encodes url, changes space etc
	// encodeURI(url + "&userLoggedIn=" + userLoggedIn)
	var encodeUrl = encodeURI(url + "&userLoggedIn=" + userLoggedIn);

	// changing main content
	console.log(encodeUrl);
	$("#mainContent").load(encodeUrl);
	$("body").scrollTop(0); // on change scrolls top

	history.pushState(null, null, url);

}


// remove song
function removeFromPlaylist(button, playlistId){
	var songId = $(button).prevAll(".songId").val();
	$.post("includes/handlers/ajax/removeFromPlaylist.php", { playlistId: playlistId, songId: songId })
	.done(function(error){

		if(error != ""){
			alert(error);
		}

		openPage("playlist.php?id=" + playlistId);
	});
}


function createPlaylist() {
 
	var popup = prompt("Please enter the name of your playlist");
 
	if(popup != null) {
 
		$.post("includes/handlers/ajax/createPlaylist.php", { name: popup, username: userLoggedIn })
		.done(function(error) {
 
			if(error != "") {
				alert(error);
				return;
			}
 
			//do something when ajax returns
			openPage("yourMusic.php");
		});
 
	}
}


// delete playlist function
function deletePlaylist(playlistId){
	var prompt = confirm("Are you sure you want to delete this playlist?");
	if(prompt == true){
		$.post("includes/handlers/ajax/deletePlaylist.php", { playlistId: playlistId })
		.done(function(error){

			if(error != ""){
				alert(error);
			}

			openPage("yourMusic.php");
		});
	}
}

// hide options menu
function hideOptionsMenu(){
	var menu = $(".optionsMenu");

	
	if(menu.css("display") != "none"){
		menu.css("display", "none");
	}
}


// options menu for songs
// everytime the options menu is shown it will take the songId and will put that into the optionsmenu
function showOptionsMenu(button){

	// this will find in tracklistrow trackOptions the optionsButton, and its ancestor is value=$albumSong->getId()
	// prevAll goes up multiple if needed, for example there is a <p> between them, prev() will stop at <p>
	var songId = $(button).prevAll(".songId").val();
	var menu = $(".optionsMenu");
	var menuWidth = menu.width();

	// takes the options menu then goes and finds the songId which is the hidden input field in optionsMenu and sets its value
	// to songId
	menu.find(".songId").val(songId);

	// takes the pos from the top of your scroll window and how far away that is from the top of the actual document
	// tldr: distance from top of window to top of doc.
	var scrollTop = $(window).scrollTop();

	// need jquery obj because it is right now an html element so we can use jquery methods on it.
	// gets the pos of the button from top of the doc, if 400px than top of button will be 400px
	var elementOffset = $(button).offset().top;

	// calc the new pos of the options menu
	// elementoffset: distance from the button to the top of the doc
	var top = elementOffset - scrollTop;

	var left = $(button).position().left;
	menu.css({ "top": top + "px", "left": left - menuWidth + "px", "display": "inline" });
}


// track timer formating
function formatTime(seconds){
	var time = Math.round(seconds);
	var minutes = Math.floor(time / 60); // rounds number down
	var seconds = time - (minutes * 60);

	var extraZero = (seconds < 10) ? "0" : "";

	return minutes + ":" + extraZero + seconds;
}

// track timers
function updateTimeProgressBar(audio){
	$(".progressTime.current").text(formatTime(audio.currentTime));
	$(".progressTime.remaining").text(formatTime(audio.duration - audio.currentTime));

	// track's progressbar
	var progress = audio.currentTime / audio.duration * 100;
	$(".playbackBar .progress").css("width", progress +"%");
}

// volume bar
function updateVolumeProgressBar(audio){
	// volume is 0 - 1
	var volume = audio.volume * 100;
	$(".volumeBar .progress").css("width", volume +"%");
}

// artist page main play button
function playFirstSong(){

	// this works because it is already created the playlist
	setTrack(tempPlayList[0], tempPlayList, true);
}

// audio
function Audio() {

	// play audio
	this.currentlyPlaying;
	this.audio = document.createElement('audio');

	// playing next song when previous one ends
	this.audio.addEventListener("ended", function(){
		nextSong();
	});

	// audio element része a canplay
	// eventlistener: if this "canplay" happens, than function happens.
	this.audio.addEventListener("canplay", function(){

		// this refers to the obj that the event was called on.
		var duration = formatTime(this.duration);
		 $(".progressTime.remaining").text(duration);
		 
	});

	this.audio.addEventListener("timeupdate", function(){
		if(this.duration){
			updateTimeProgressBar(this);
		}
	});

	this.audio.addEventListener("volumechange", function(){
		updateVolumeProgressBar(this);
	});

	this.setTrack = function(track) { // track JSON
		this.currentlyPlaying = track; // keep track of currently playing song
		this.audio.src = track.path;
	}

	this.play = function() {

		//this.audio.play();
		// FOR CHROME ONLY PROMISE
		var promise = this.audio.play();

	
		if (promise !== undefined) {
	    	promise.then(_ => {

	        // Autoplay started!
	    	}).catch(error => {

	        // Autoplay was prevented.
	        
	    	});
		}
		
	}

	this.pause = function() {
		this.audio.pause();
	}

	this.setTime = function(seconds){
		this.audio.currentTime = seconds;
	}

}