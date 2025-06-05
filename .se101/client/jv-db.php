<?php
$host = 'localhost'; // Your database host
$dbname = 'nattendance'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password (default is empty for local setups)

try {
    // Create a new PDO instance for the database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If the connection fails, show an error message
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
