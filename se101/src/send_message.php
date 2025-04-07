<?php
require_once '../src/config.php';
$data = json_decode(file_get_contents("php://input"), true);

$sender = $data['sender'];
$receiver = $data['receiver'];
$message = $data['message'];

if ($sender && $receiver && $message) {
    $stmt = $pdo->prepare("INSERT INTO messages (sender, receiver, message) VALUES (?, ?, ?)");
    if ($stmt->execute([$sender, $receiver, $message])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'DB error']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing data']);
}
?>
