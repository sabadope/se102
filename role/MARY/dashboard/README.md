HINDI PA YAN YUNG LITERAL NA DESIGN HA

BASTA PARANG GANYAN LANG NAISIP KO NA CONCEPT THEN TRY LANG YUNG PAGCONNECT KO SA DALAWANG HTML 
BABAGUHIN KO PA YUNG MANUAL ADDING OF INTERNS.

YUN LANG HSHHSHS

http://localhost/se102/role/MARY/intern_eval/login.php

<?php
$host = "localhost";
$user = "root";
$pass = ""; // Default XAMPP MySQL password is empty
$dbname = "intern_eval";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
