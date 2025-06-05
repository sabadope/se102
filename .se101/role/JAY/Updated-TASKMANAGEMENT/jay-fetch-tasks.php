<?php
// Database Connection
$conn = new mysqli('localhost', 'root', '', 'task_management');

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch tasks from the database
$sql = "SELECT * FROM tasks";
$result = $conn->query($sql);

$tasks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

// Return tasks as JSON
echo json_encode($tasks);
?>
