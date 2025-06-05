<?php
session_start();
session_unset();
session_destroy();
header("Location: cha-login.php");
exit();
?>
