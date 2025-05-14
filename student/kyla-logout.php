<?php
session_start();
session_destroy();
header("Location: SkillNavigator/student-skilldevelopment.php");
exit();
?>