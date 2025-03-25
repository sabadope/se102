<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'feedback_system';
$user = 'root';
$pass = '';  

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Generate session token
        $session_token = bin2hex(random_bytes(32));
        
        $sql = "INSERT INTO user_sessions (user_id, session_token, expires_at)
                VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user['id'], $session_token);
        $stmt->execute();
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['session_token'] = $session_token;

        // Redirect based on role
        switch ($user['role']) {
            case 'admin':
                header('Location: admin_panel.php');
                break;
            case 'supervisor':
                header('Location: supervisor_feedback.php');
                break;
            case 'customer':
                header('Location: customer_feedback.php');
                break;
            case 'intern':
                header('Location: intern_dashboard.php');
                break;
        }
    } else {
        echo "Invalid credentials!";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Password:</label>
        <input type="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>
