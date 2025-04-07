<?php
// Start the session and include your database connection
session_start();
$conn = new mysqli('localhost', 'root', '', 'task_management');

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the task ID and feedback from the form
$task_id = $_POST['task_id'];
$feedback = $_POST['feedback'] ?? ''; // Default to empty string if no feedback is provided

// Update the task's feedback in the database
$sql = "UPDATE tasks SET feedback = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $feedback, $task_id);
$stmt->execute();
$stmt->close();

// Redirect back to the supervisor view (or another page)
header('Location: supervisor-view.php');
exit;
?>
