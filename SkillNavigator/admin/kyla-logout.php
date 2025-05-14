<?php
// Logout script
<<<<<<< HEAD
require_once "includes/kyla-auth.php";
=======
require_once "includes/auth.php";
>>>>>>> 2968422a81397f35ab70259736958ea77141521b

// Log out the user
logout_user();

// Redirect to login page
$_SESSION['success'] = "You have been successfully logged out.";
<<<<<<< HEAD
header("Location: kyla-login.php");
=======
header("Location: login.php");
>>>>>>> 2968422a81397f35ab70259736958ea77141521b
exit;
?>
