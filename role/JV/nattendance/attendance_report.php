<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != 'supervisor') {
    echo "Access denied.";
    exit;
}

$sql = "SELECT * FROM attendance WHERE status = 'pending'"; // Adjust for pending approval status
$stmt = $pdo->query($sql);
$attendance_records = $stmt->fetchAll();

foreach ($attendance_records as $record) {
    echo "User: " . $record['user_id'] . " - Status: " . $record['status'] . "<br>";
    // Provide approval options
}
?>
