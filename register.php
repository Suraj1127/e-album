<?php

ob_start();
session_start();
if (isset($_SESSION['user'])!="") {
	header("Location: home.php");
}
include_once 'dbconnect1.php';
$currentPage = "register";

$email = "";
$pass = "";
$name = "";
$nameError = "";
$emailError = "";
$passError = "";
$error = false;
if (isset($_POST['btn-signup'])) {
	$name = trim($_POST['name']);
	$name = strip_tags($name);
	$name = htmlspecialchars($name);

	$email = trim($_POST['email']);
	$email = strip_tags($email);
	$email = htmlspecialchars($email);

	$pass = trim($_POST['pass']);
	$pass = strip_tags($pass);
	$pass = htmlspecialchars($pass);

	if (empty($name)) {
		$error = true;
		$nameError = "Please enter your full name.";
	} else if (strlen($name) < 3) {
		$error = true;
		$nameError = "Name must have at least 3 characters";
	} else if (!preg_match("/^[a-zA-Z0-9]+$/", $name)) {
		$error = true;
		$nameError = "Name must contain alphabets and numbers only.";
	} else {
		$query = "SELECT userName FROM users WHERE userName = '$name'";
		$res = mysqli_query($conn, $query);
		if (mysqli_num_rows($res) != 0) {
			$error = true;
			$nameError = "Username is already taken.  Please enter another username.";
		}

	}
	
	//basic email validation
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = true;
		$emailError = "Please enter valid email address";
	} else {
		//check email exists or not
		$query = "SELECT userEmail FROM users WHERE userEmail = '$email'";
		$result = mysqli_query($conn, $query);
		$count = mysqli_num_rows($result);
		if ($count != 0) {
			$error = true;
			$emailError = "Provided Email is already in use.";
		}
	}

	//password validation
	if (empty($pass)) {
		$error = true;
		$passError = "Please enter password.";
	} else if(strlen($pass) < 6) {
		$error = true;
		$passError = "Password must have at least 6 characters.";
	} 

	//password encrypt using SHA256();
	$password = hash('sha256', $pass);

	//if there is no error, continue to signup
	if (!$error) {
		$query = "INSERT INTO users(userName,userEmail,userPass) VALUES('$name','$email','$password')";
		$res = mysqli_query($conn, $query);

		if ($res) {
			$errTyp = "success";
			$_SESSION['message'] = " Successfully registered, you may login now.";
			$directory = "uploads/".$name;
			mkdir($directory);
			$query = "SELECT * FROM users WHERE userName = '$name'";
			$res = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($res);
			$_SESSION['user'] = $row['userId'];
			header("Location: home.php");
			$name = "";
			$email = "";
			$pass = "";
		} else {
			$errTyp = "danger";
			$errMSG = "Something went wrong, try again later...";
		}
	} 

}
?>


<!DOCTYPE html>
<html>
<head>
<meta property="og:image" content="http://creatovert.com/album/display.jpg" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Register - Album</title>
<link rel="icon" href="/album/logo.png">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<link rel="stylesheet" href="style.php" type="text/css" />
<link rel="stylesheet" type="text/css" href="lightbox.css">
<style type = "text/css">
	body {
    background-color: #FAFAFA;
  }
</style>

</head>
<body>

 <?php include "header.php"; ?>
 	<div id = "wrapper">
	<div class = "container text-center">
		<div id = "login-form" style = "max-width: 500px; margin: 5% auto;">
			<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">
				<div class = "col-md-12">
					<div class = "form-group">
						<h2 class = "">Sign Up</h2>
					</div>
					<div class = "form-group">
						<hr>
					</div>
					<?php
					if ( isset($errMSG) ) {
		    
				    ?>
				    <div class="form-group">
				             <div class="alert alert-<?php echo ($errTyp=="success") ? "success" : $errTyp; ?>">
				    <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
				                </div>
				             </div>
				                <?php
				   }
				   ?>
				            
				            <div class="form-group">
				                <div class="input-group">
						            <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
						            <input type="text" name="name" class="form-control" placeholder="Enter Name" maxlength="50" value="<?php echo $name; ?>" >
				                </div>
				                <span class="text-danger"><?php echo $nameError; ?></span>
				            </div>
				            
				            <div class="form-group">
				             <div class="input-group">
				                <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
				             <input type="email" name="email" class="form-control" placeholder="Enter Your Email" maxlength="40" value="<?php echo $email ?>" />
				                </div>
				                <span class="text-danger"><?php echo $emailError; ?></span>
				            </div>
				            
				            <div class="form-group">
				             <div class="input-group">
				                <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
				             <input type="password" name="pass" class="form-control" placeholder="Enter Password" maxlength="15" />
				                </div>
				                <span class="text-danger"><?php echo $passError; ?></span>
				            </div>
				            
				            <div class="form-group">
				             <hr />
				            </div>
				            
				            <div class="form-group">
				             <button type="submit" class="btn btn-block btn-primary" name="btn-signup">Sign Up</button>
				            </div>
				            
				            <div class="form-group">
				             <hr />
				            </div>
				            
				            <div class="form-group">
				             <a href="index.php">Sign in Here...</a>
				            </div>
				        
				        </div>
				   
				    </form>
				    </div> 

				</div>
				</div>
				</body>
				</html>
				<?php ob_end_flush(); ?>