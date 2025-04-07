<?php
session_start();
require_once '../src/config.php';

$current_user = $_SESSION['username'];
$chatWith = $_POST['chatWith'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM messages WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?) ORDER BY timestamp ASC");
$stmt->execute([$current_user, $chatWith, $chatWith, $current_user]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
?>
