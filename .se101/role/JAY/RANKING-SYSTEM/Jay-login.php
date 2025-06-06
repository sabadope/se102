<?php
require 'db_connect.php'; // Make sure the correct path is provided

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Check if fields are empty
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Use PDO to fetch user details
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Check if the user exists and password matches
        if ($user && $password === $user["password"]) { // Match password directly (for testing)
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["role"] = $user["role"];

            // Redirect based on role
            if ($user["role"] == "Intern") {
                header("Location: Jay-intern-viewranking.php");
            } else {
                header("Location: Jay-supervisorranking-system.php");
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card p-4 shadow" style="width: 350px;">
        <h3 class="text-center mb-3">Login</h3>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form action="jay-login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" class="form-control" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>

            <button type="submit" class="btn btn-primary mt-3 w-100">Login</button>
        </form>
    </div>
</body>
</html>
