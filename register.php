<?php 
	include("includes/config.php");
	include("includes/classes/Account.php");
	include("includes/classes/Constants.php");
	$account = new Account($con);

	include("includes/handlers/register-handler.php");
	include("includes/handlers/login-handler.php");

	function getInputValue($name){
		if(isset($_POST[$name])){
			echo $_POST[$name];
		}
	}
?>

<html>
<head>
	<title>Slotify Reg</title>

	<link rel="stylesheet" type="text/css" href="assets/css/register.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>
</head>
<body>
	<?php 
	if(isset($_POST['registerButton'])){
		echo '<script>
				$(document).ready(function(){

					$("#loginForm").hide();
					$("#registerForm").show();
				});
			</script>';
	} else {
		echo '<script>
				$(document).ready(function(){

					$("#loginForm").show();
					$("#registerForm").hide();
				});
			</script>';
	}


	?>
	
	<div id="background">
		<div id="loginContainer">
			<div id="inputContainer">
				<form action="register.php" method="POST" accept-charset="utf-8" id="loginForm">
					<h2>Login to your account</h2>
					<p>
						<?php echo $account->getError(Constants::$loginFailed); ?>
						<label for="loginUsername">Username</label>
						<input type="text" name="loginUsername" id="loginUsername" placeholder="e.g. Your Name" required value="<?php getInputValue('loginUsername') ?>">

					</p>
					<p>
						<label for="loginPassword">Password</label>
						<input type="password" name="loginPassword" id="loginPassword" placeholder="Your password" required>

					</p>
					<button type="submit" name="loginButton">Login</button>
					<div class="hasAccountText">
						<span id="hideLogin">Don't have an account yet? Sign up here!</span>
					</div>
				</form>

				<form action="register.php" method="POST" accept-charset="utf-8" id="registerForm">
					<h2>Create your account</h2>
					<p>
						<?php echo $account->getError(Constants::$usernameCharacters); ?>
						<?php echo $account->getError(Constants::$usernameTaken); ?>
						<label for="userName">Username</label>
						<input type="text" name="username" id="username" placeholder="e.g. Your Name" value="<?php getInputValue('username') ?>">

					</p>

					<p>
						<?php echo $account->getError(Constants::$firstNameCharacters); ?>
						<label for="firstName">First Name</label>
						<input type="text" name="firstName" id="firstName" placeholder="e.g. Bart" value="<?php getInputValue('firstName') ?>">

					</p>

					<p>
						<?php echo $account->getError(Constants::$lastNameCharacters); ?>
						<label for="lastName">Last Name</label>
						<input type="text" name="lastName" id="lastName" placeholder="e.g. Simpson" value="<?php getInputValue('lastName') ?>">

					</p>

					<p>
						<?php echo $account->getError(Constants::$emailsDoNotMatch); ?>
						<?php echo $account->getError(Constants::$emailInvalid); ?>
						<?php echo $account->getError(Constants::$emailTaken); ?>
						<label for="email">Your Email</label>
						<input type="email" name="email" id="email" placeholder="e.g. Email adress" value="<?php getInputValue('email') ?>">

					</p>

					<p>	
						<label for="email2">Confirm Email</label>
						<input type="email" name="email2" id="email2" placeholder="Confirm Email adress" value="<?php getInputValue('email2') ?>">

					</p>

					<p>
						<?php echo $account->getError(Constants::$passwordsDoNotMatch); ?>
						<?php echo $account->getError(Constants::$passwordNotAlphanumeric); ?>
						<?php echo $account->getError(Constants::$passwordCharacters); ?>
						<label for="password">Password</label>
						<input type="password" name="password" id="password" placeholder="Your password" >

					</p>

					<p>
						
						<label for="password2">Confirm password</label>
						<input type="password" name="password2" id="password2" placeholder="Your password" >

					</p>
					<button type="submit" name="registerButton">Sign Up!</button>
					<div class="hasAccountText">
						<span id="hideRegister">Already have an account? Log in here!</span>
					</div>
				</form>
			</div>

			<div id="loginText">
				<h1>Get great music!</h1>
				<h2>Listen to loads of songs for free!</h2>
				<ul>
					<li>Discover new music</li>
					<li>Create your own playlist</li>
					<li>Follow artist to keep up to date</li>
				</ul>
			</div>

		</div>
	</div>
</body>
</html>