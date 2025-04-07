<?php
require_once '../src/config.php';
session_start();

$loggedInId = $_SESSION['user_id'];
$withUser = $_POST['chatWith'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM messages WHERE sender = ? OR receiver = ? ORDER BY timestamp ASC");
$stmt->execute([$withUser]);
$receiver = $stmt->fetch();

if ($receiver) {
    $receiver_id = $receiver['id'];

    // Get messages between both users
    $stmt = $pdo->prepare("SELECT m.*, u.username AS sender_name 
                           FROM messages m
                           JOIN users u ON m.sender_id = u.id
                           WHERE (sender_id = :me AND receiver_id = :them)
                              OR (sender_id = :them AND receiver_id = :me)
                           ORDER BY m.timestamp ASC");
    $stmt->execute(['me' => $loggedInId, 'them' => $receiver_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($messages);
}
