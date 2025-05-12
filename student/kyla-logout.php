<?php
// Logout script
require_once "includes/kyla-auth.php";

// Log out the user
logout_user();

// Redirect to login page
$_SESSION['success'] = "You have been successfully logged out.";
header("Location: kyla-login.php");
exit;
?>
