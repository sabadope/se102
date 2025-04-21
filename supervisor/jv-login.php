<?php
session_start();
require_once 'jv-db.php'; // Make sure this provides $pdo

// Hardcoded Supervisor Account
$supervisor_username = 'supervisor';
$supervisor_password = 'supervisor1230';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.location.href='jv-login.php';</script>";
        exit;
    }

    // 1. Check if this is the hardcoded Supervisor account
    if ($username === $supervisor_username && $password === $supervisor_password) {
        $_SESSION['user_id'] = 0; // 0 for hardcoded account
        $_SESSION['username'] = $supervisor_username;
        $_SESSION['role'] = 'supervisor';

        header("Location: jv-supervisor_dashboard.php");
        exit;
    }

    // 2. Otherwise, check in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'supervisor') {
            header("Location: jv-supervisor_dashboard.php");
        } else {
            header("Location: jv-intern_dashboard.php");
        }
        exit;
    } else {
        echo "<script>alert('Invalid username or password!'); window.location.href='jv-login.php';</script>";
        exit;
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Body */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(145deg, #e3f2fd, #f1f8e9);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Login Container */
        .login-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }

        /* Heading */
        h2 {
            margin-bottom: 30px;
            color: #333;
            font-size: 24px;
            font-weight: 600;
        }

        /* Form Inputs */
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box; /* Ensure padding doesn't affect width */
        }

        /* Form Inputs */
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box; /* Ensure padding doesn't affect width */
        }

        /* Focus Effect */
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.6);
        }

        /* Submit Button */
        input[type="submit"] {
            width: 100%;
            padding: 15px;
            margin-top: 10px;
            background-color: #4CAF50;
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        /* Hover Effect on Submit */
        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Error Message */
        .error-message {
            color: red;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 500;
        }

        /* Sign Up Button */
        .signup-btn {            
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 89%;
        }

        /* Hover Effect on Sign Up Button */
        .signup-btn:hover {
            background-color: #0056b3;
        }

        /* Media Query for Mobile Devices */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px;
                width: 90%;
            }

            h2 {
                font-size: 20px;
            }

            input[type="text"], input[type="password"], input[type="submit"] {
                font-size: 14px;

            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>User Authentication</h2>
        <form method="POST" action="jv-login.php">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Verified">
        </form>

    </div>
</body>
</html>
