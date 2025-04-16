<?php
// update-task-status.php

// Include the database connection file
include('db_connect.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['task_id'];
    $status = $_POST['status'];

    // Validate the input (you can add additional validation as needed)
    if (empty($taskId) || empty($status)) {
        echo "Task ID and Status are required.";
        exit;
    }

    // Prepare the SQL query to update the task status in the database
    $query = "UPDATE tasks SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        // If the query preparation fails
        echo "Error preparing the query.";
        exit;
    }

    // Bind the parameters to the query
    $stmt->bind_param('si', $status, $taskId);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        echo "Task status updated successfully.";
    } else {
        echo "Failed to update task status.";
    }

    // Close the statement
    $stmt->close();
} else {
    // If the request method is not POST, show an error
    echo "Invalid request method.";
}

// Close the database connection
$conn->close();
?>
