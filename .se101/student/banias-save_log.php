<?php
session_start();
include 'banias-db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: banias-login.php");
    exit();
}

// Get user_id from session
$user_id = (int)$_SESSION['user_id'];

// Verify user exists
$check_user = $conn->query("SELECT id FROM users WHERE id = $user_id");
if ($check_user->num_rows === 0) {
    die("Error: Invalid user ID");
}

// Prepare data
$task_name = isset($_POST['task_name']) ? $conn->real_escape_string($_POST['task_name']) : null;
$task_desc = isset($_POST['task_desc']) ? $conn->real_escape_string($_POST['task_desc']) : null;
$start_time = isset($_POST['start_time']) ? $conn->real_escape_string($_POST['start_time']) : null;
$end_time = isset($_POST['end_time']) ? $conn->real_escape_string($_POST['end_time']) : null;
$status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : 'Pending';
$weekly_goals = isset($_POST['weekly_goals']) ? $conn->real_escape_string($_POST['weekly_goals']) : null;
$achievements = isset($_POST['achievements']) ? $conn->real_escape_string($_POST['achievements']) : null;
$challenges = isset($_POST['challenges']) ? $conn->real_escape_string($_POST['challenges']) : null;
$lessons = isset($_POST['lessons']) ? $conn->real_escape_string($_POST['lessons']) : null;
$timestamp = date('Y-m-d H:i:s'); // Add current timestamp

// Validate required fields
if (empty($task_name) || empty($start_time) || empty($end_time)) {
    die("Error: Required fields are missing");
}

$sql = "INSERT INTO logs (user_id, task_name, task_desc, start_time, end_time, status, weekly_goals, achievements, challenges, lessons, timestamp)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issssssssss", 
    $user_id, 
    $task_name, 
    $task_desc, 
    $start_time, 
    $end_time, 
    $status, 
    $weekly_goals, 
    $achievements, 
    $challenges, 
    $lessons,
    $timestamp
);

if ($stmt->execute()) {
    header("Location: banias-index.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>