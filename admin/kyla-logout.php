<?php
session_start();
session_destroy();
header("Location: SkillNavigator/admin-skilldevelopment.php");
exit();
?>