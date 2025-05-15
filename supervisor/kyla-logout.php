<?php
session_start();
session_destroy();
header("Location: SkillNavigator/supervisor-skilldevelopment.php");
exit();
?>