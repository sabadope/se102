<?php
session_start();

// Database configuration
$host = 'localhost';
$db = 'feedback_system';
$user = 'root';
$pass = '';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) { // Added closing parenthesis
    header("Location: cha-login.php");
    exit();
}

// Verify session token
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "SELECT * FROM user_sessions 
        WHERE user_id = ? AND session_token = ? AND expires_at > NOW()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $_SESSION['user_id'], $_SESSION['session_token']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_unset();
    session_destroy();
    header("Location: cha-login.php");
    exit();
}

$stmt->close();
$conn->close();
?>