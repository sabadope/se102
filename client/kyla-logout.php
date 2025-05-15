<?php
session_start();
session_destroy();
header("Location: SkillNavigator/client-skilldevelopment.php");
exit();
?>