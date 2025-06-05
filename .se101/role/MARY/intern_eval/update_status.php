<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Uncomment the line below to see the contents of the $_POST array
    // var_dump($_POST);

    // Check if 'id' and 'status' are set in the POST request
    if (isset($_POST['id']) && isset($_POST['status'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];

        // Prepare the SQL statement
        $stmt = $conn->prepare("UPDATE hiring_evaluations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        
        // Execute the statement and check for success
        if ($stmt->execute()) {
            echo "Status updated successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: ID or status not set.";
    }
}

// Close the database connection
$conn->close();
?>