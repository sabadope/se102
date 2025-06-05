<?php
session_start();
require 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: dashboard.php");
    } else {
        echo "Invalid login";
    }
}
?>

<form method="post">
  Email: <input name="email"><br>
  Password: <input type="password" name="password"><br>
  <button type="submit">Login</button>
</form>