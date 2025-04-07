<?php
// Start the session and include your database connection
session_start();
$conn = new mysqli('localhost', 'root', '', 'task_management');

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the task ID, rating, and feedback from the form
$task_id = $_POST['task_id'];
$rating = $_POST['rating'] ?? null;  // Default to null if no rating is provided
$feedback = $_POST['feedback'] ?? '';  // Default to empty string if no feedback is provided

// Update both the rating and feedback in the database
if ($rating !== null || $feedback !== '') {
    $sql = "UPDATE tasks SET rating = ?, feedback = ? WHERE id = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Check if prepare was successful
    if ($stmt === false) {
        die('Error preparing statement: ' . $conn->error);
    }

    // Bind parameters (rating, feedback, task_id)
    $stmt->bind_param('isi', $rating, $feedback, $task_id);

    // Execute the statement
    $stmt->execute();

    // Close the statement
    $stmt->close();
}

// Redirect to intern-view.php after updating the rating and feedback
header('Location: intern-view.php');
exit;
?>
