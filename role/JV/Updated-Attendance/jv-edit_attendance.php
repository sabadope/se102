<?php
include 'jv-db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $status = $_POST['status'];

    // Update attendance in the database
    $sql = "UPDATE attendance SET check_in = ?, check_out = ?, status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$check_in, $check_out, $status, $id]);

    header('Location: jv-supervisor_dashboard.php'); // Redirect back to the dashboard
    exit;
}
