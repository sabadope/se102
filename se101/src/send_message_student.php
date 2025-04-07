<?php
session_start();
require_once '../src/config.php';

$sender = $_SESSION['username'];
$receiver = $_POST['receiver'] ?? '';
$message = $_POST['message'] ?? '';

if ($message && $receiver) {
    $stmt = $pdo->prepare("INSERT INTO messages (sender, receiver, message, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$sender, $receiver, $message]);
    echo "Message sent";
} else {
    echo "Missing message or receiver";
}
?>
