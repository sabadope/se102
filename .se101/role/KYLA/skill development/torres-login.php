<?php
session_start();


// Hardcoded Admin Credentials
$valid_username = 'admin';
$valid_email = 'admin@gmail.com';
$valid_password = 'admin1230';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {
        // Check if user is logging in as the hardcoded Admin
        if ($email === $valid_email && $password === $valid_password) {
            $_SESSION['user_id'] = 1; // Set a dummy ID for Admin
            $_SESSION['username'] = $valid_username;
            $_SESSION['email'] = $valid_email;
            $_SESSION['role'] = 'Admin';

            header("Location: index.php");
            exit();
        }

        // Otherwise, check the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'Admin') {
                header("Location: index.php");
            } elseif ($user['role'] === 'Student') {
                header("Location: index.php");
            } elseif ($user['role'] === 'Supervisor') {
                header("Location: index.php");
            } elseif ($user['role'] === 'Client') {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid email or password!";
        }
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
    <title>Login</title>
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

        .login-container {
            background: var(--light);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 320px;
            text-align: center;
        }

        .login-form h2 {
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

        .input-group input {
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

        .register-link {
            margin-top: 10px;
            font-size: 14px;
        }

        .register-link a {
            color: var(--blue);
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <form class="login-form" action="login.php" method="POST">
            <h2>Login</h2>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
            <p class="register-link">Don't have an account? <a href="register.php">Register</a></p>
        </form>
    </div>
</body>
</html>
