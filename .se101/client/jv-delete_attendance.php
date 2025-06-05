<?php
session_start();
include 'jv-db.php'; // Database connection

// Ensure user is logged in and is a supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header('Location: jv-login.php');
    exit;
}

// Check if ID is set and it's a valid integer
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the SQL to delete the record
    $sql = "DELETE FROM attendance WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    // Execute the deletion
    if ($stmt->execute([$id])) {
        // Redirect to the same page after successful deletion
        header('Location: jv-supervisor_dashboard.php');
    } else {
        echo 'Error deleting the attendance record.';
    }
} else {
    echo 'Invalid ID parameter.';
}
?>
