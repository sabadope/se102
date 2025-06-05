<?php
$host = '127.0.0.1:3307'; // not 'localhost' if using custom port
$user = 'root';
$pass = ''; // default XAMPP password
$db = 'intern_eval';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
