<?php 
	class Account {

		private $con;
		private $errorArray;

		public function __construct($con){
			$this->con = $con;
			$this->errorArray = array(); // set it to empty array
			
		}

		public function login($un, $pw){
			$pw = md5($pw); // encrypt the pw to see encrypted pw match.
			$query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$un' AND password='$pw'");
			// if one result found
			if(mysqli_num_rows($query) == 1){
				return true;
			} else {
				array_push($this->errorArray, Constants::$loginFailed);
				return false;
			}

		}

		public function register($un, $fn, $ln, $em, $em2, $pw, $pw2) {
			$this->validateUsername($un);
			$this->validateFirstrname($fn);
			$this->validateLastname($ln);
			$this->validateEmails($em, $em2);
			$this->validatePasswords($pw, $pw2);

			// checks if empty, returns true or false
			if(empty($this->errorArray) == true){
				// insert into database
				return $this->insertUserDetails($un, $fn, $ln, $em, $pw);
			}

			else {
				return false;
			}
		}

		public function getError($error){
			// checks if the given parameter exists in this array
			if(!in_array($error, $this->errorArray)){
				$error = "";
			}
			return "<span class='errorMessage'>$error</span>";
		}

		private function insertUserDetails($un, $fn, $ln, $em, $pw){
			$encryptedPw = md5($pw);
			$profilePic = "assets/images/profile-pics/head_emerald.png";
			$date = date("Y-m-d");
			// id, username, firstName, etc, has to mach with database table. id is empty because of auto increment
			$result = mysqli_query($this->con, "INSERT INTO users VALUES 
				(NULL, '$un', '$fn', '$ln', '$em', '$encryptedPw', '$date', '$profilePic')");
			return $result;
		}

		private function validateUsername($un) {
			// strlen = string length
			if(strlen($un) > 25 || strlen($un) < 5){
				array_push($this->errorArray, Constants::$usernameCharacters);
				return;
			}

			// check if username exists.
			$checkUsernameQuery = mysqli_query($this->con, "SELECT username FROM users WHERE username='$un'");
			if(mysqli_num_rows($checkUsernameQuery) != 0){
				array_push($this->errorArray, Constants::$usernameTaken);
				return;
			}
		}

		private function validateFirstrname($fn) {
			if(strlen($fn) > 25 || strlen($fn) < 2){
				array_push($this->errorArray, Constants::$firstNameCharacters);
				return;
			}
		}

		private function validateLastname($ln) {
			if(strlen($ln) > 25 || strlen($ln) < 2){
				array_push($this->errorArray, Constants::$lastNameCharacters);
				return;
			}
		}

		private function validateEmails($em, $em2) {
			if($em != $em2) {
				array_push($this->errorArray, Constants::$emailsDoNotMatch);
				return;
			}
			if(!filter_var($em, FILTER_VALIDATE_EMAIL)){
				array_push($this->errorArray, Constants::$emailInvalid);
				return;
			}
			// check if emails isnt been used.
			$checkEmailQuery = mysqli_query($this->con, "SELECT email FROM users WHERE email='$em'");
			if(mysqli_num_rows($checkEmailQuery) != 0){
				array_push($this->errorArray, Constants::$emailTaken);
				return;
			}
		}

		private function validatePasswords($pw, $pw2) {
			if($pw != $pw2){
				array_push($this->errorArray, Constants::$passwordsDoNotMatch);
				return;
			}
			// if password matches this pattern ('regex') then to this.
			if(preg_match('/[^A-Za-z0-9]/', $pw)){
				array_push($this->errorArray, Constants::$passwordNotAlphanumeric);
				return;
			}
			if(strlen($pw) > 30 || strlen($pw) < 5){
				array_push($this->errorArray, Constants::$passwordCharacters);
				return;
			}

		}
	}
?>