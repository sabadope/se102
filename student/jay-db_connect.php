<?php
$host = "localhost";  // Change this if your database is hosted elsewhere
$username = "root";   // Change this to your MySQL username
$password = "";       // Change this to your MySQL password (leave empty if no password)
$database = "task_management"; // Change this to your actual database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
