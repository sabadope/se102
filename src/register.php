<?php
session_start();
require_once '../src/config.php';

// Debugging: Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role'];

    // Debugging: Check if all fields are received
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        die("All fields are required!");
    }

    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Debugging: Display the query before executing
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password, $role]);

        // Debugging: Check if the user was inserted successfully
        if ($stmt->rowCount() > 0) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role === 'Student') {
                header("Location: ../student/student-dashboard.php");
            } elseif ($role === 'Client') {
                header("Location: ../client/client-dashboard.php");
            }
            exit();
        } else {
            die("Error: Unable to register user.");
        }
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <title>Register</title>
    <style>

        @import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --poppins: 'Poppins', sans-serif;
            --lato: 'Lato', sans-serif;

            --light: #F9F9F9;
            --blue: #3C91E6;
            --light-blue: #CFE8FF;
            --grey: #eee;
            --dark-grey: #AAAAAA;
            --dark: #342E37;
            --red: #DB504A;
            --yellow: #FFCE26;
            --light-yellow: #FFF2C6;
            --orange: #FD7238;
            --light-orange: #FFE0D3;
        }
        
        body {
            font-family: var(--poppins);
            background: var(--grey);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-container {
            background: var(--light);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }

        .register-form h2 {
            color: var(--dark);
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-weight: 600;
            color: var(--dark);
        }

        .input-group input, .input-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--dark-grey);
            border-radius: 5px;
            outline: none;
            font-size: 14px;
        }

        .btn {
            background: var(--blue);
            color: var(--light);
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: var(--light-blue);
            color: var(--dark);
        }

        .login-link {
            margin-top: 10px;
            font-size: 14px;
        }

        .login-link a {
            color: var(--blue);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: var(--red);
            font-size: 14px;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>

    <div class="register-container">
        <form class="register-form" action="register.php" method="POST">
            <h2>Register</h2>

            <?php if (isset($error)) : ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="input-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>-- Select Role --</option>
                    <option value="Student">Student</option>
                    <option value="Client">Client</option>
                </select>
            </div>
            <button type="submit" class="btn">Register</button>
            <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>

</body>
</html>