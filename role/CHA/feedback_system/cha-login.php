<?php
session_start();

$host = 'localhost';
$db = 'feedback_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, first_name, role, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Generate session token
            $session_token = bin2hex(random_bytes(32));
            
            // Store session in database
            $sql = "INSERT INTO user_sessions (user_id, session_token, expires_at)
                    VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $user['id'], $session_token);
            $stmt->execute();
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['session_token'] = $session_token;

            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    header('Location: cha-admin_panel.php');
                    break;
                case 'supervisor':
                    header('Location: cha-supervisor_feedback.php');
                    break;
                case 'customer':
                    header('Location: cha-customer_feedback.php');
                    break;
                case 'intern':
                    header('Location: cha-intern_dashboard.php');
                    break;
                default:
                    header('Location: cha-dashboard.php');
            }
            exit();
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Invalid email or password";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Feedback System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container" style="max-width: 500px;">
        <h1>Login</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form action="cha-login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="button">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 1rem;">
            Don't have an account? <a href="cha-register.php">Register here</a>
        </p>
    </div>
</body>
</html>