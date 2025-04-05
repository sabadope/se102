<?php
    require_once '../src/config.php';
    session_start();

    if (!isset($_SESSION['username']) || !isset($_POST['receiver']) || !isset($_POST['message'])) {
        http_response_code(400);
        echo 'Missing required data.';
        exit();
    }

    $sender = $_SESSION['username'];
    $receiver = $_POST['receiver'];
    $message = trim($_POST['message']);

    $stmt = $pdo->prepare("INSERT INTO messages (sender, receiver, message) VALUES (?, ?, ?)");
    $stmt->execute([$sender, $receiver, $message]);

    echo 'Message sent.';
?>
