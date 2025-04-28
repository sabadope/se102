<?php

$host = 'localhost';
$db   = 'Perfomance_rankings';
$user = 'root';
$pass = ''; // Leave empty if using default XAMPP settings
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Turn on errors in the form of exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Make the default fetch be an associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Turn off emulation mode for "real" prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Display a user-friendly error message
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>
