<?php
session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $intern_id = $conn->real_escape_string($_POST['intern_id']);
    $name = $conn->real_escape_string($_POST['name']);

    $check = $conn->query("SELECT * FROM logins WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $error = "Username already exists.";
    } else {
        // Insert into logins table
        $stmt = $conn->prepare("INSERT INTO logins (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        $stmt->execute();

        $user_id = $conn->insert_id;

        // If intern, insert into interns table
        if ($role === 'intern') {
            $stmt2 = $conn->prepare("INSERT INTO interns (intern_id, name, user_id) VALUES (?, ?, ?)");
            $stmt2->bind_param("ssi", $intern_id, $name, $user_id);
            $stmt2->execute();
        }

        header("Location: login.php");
        exit;
    }
}
?>

<!-- HTML form below -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>
    <div class="auth-container">
        <h2>Register</h2>
        <form method="POST">
            <!-- Only show these if intern is selected -->
            <input type="text" name="intern_id" placeholder="Intern ID" required>
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <select name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="supervisor">Supervisor</option>
                <option value="intern">Intern</option>
            </select><br>
            <button type="submit">Register</button>
            <p>Already have an account? <a href="login.php">Login</a></p>
            <?php if ($error): ?>
                <p style="color:red;"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
