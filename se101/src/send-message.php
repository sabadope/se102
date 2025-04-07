<?php
session_start();
require 'db.php'; // connection

$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data['message']);
$receiver = trim($data['receiver']);
$sender = $_SESSION['username']; // assume login stores this

if ($message && $receiver) {
    $stmt = $pdo->prepare("INSERT INTO messages (sender, receiver, message) VALUES (?, ?, ?)");
    $success = $stmt->execute([$sender, $receiver, $message]);

    echo json_encode(['success' => $success]);
}
