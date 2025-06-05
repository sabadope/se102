<?php
session_start();
require_once 'banias-db_connect.php'; // Connect to your database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Support both hashed passwords and plain text passwords
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id'];  // Store user ID in session
            $_SESSION['role'] = $user['role'] ?? 'user';
            
            // If using plain text password, update to hashed version
            if ($password === $user['password']) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_pwd_query = "UPDATE users SET password = ? WHERE id = ?";
                $update_pwd_stmt = $conn->prepare($update_pwd_query);
                $update_pwd_stmt->bind_param("si", $hashed_password, $user['id']);
                $update_pwd_stmt->execute();
            }
            
            // Update last login time
            $update_query = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();
            
            // Redirect based on role
            if ($_SESSION['role'] === 'admin') {
                header('Location: banias-admin.php');
            } else {
                header('Location: banias-index.php');
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
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
        :root {
            --primary: #4CAF50;
            --primary-dark: #45a049;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --danger: #f72585;
            --border-radius: 8px;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }
        
        .login-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-header {
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: var(--gray);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }
        
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .checkbox-group input {
            width: auto;
            margin-right: 0.5rem;
        }
        
        .btn {
            width: 100%;
            padding: 0.8rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            background: var(--primary);
            color: white;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .error-message {
            color: var(--danger);
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
            }
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
            text-align: center;
            width: 100%;
        }
        
        /* Add success message styling */
        .success-message {
            color: var(--primary);
            margin-top: 1rem;
            font-size: 0.9rem;
            background-color: rgba(76, 175, 80, 0.1);
            padding: 10px;
            border-radius: var(--border-radius);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>User Authentication</h1>
            
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success-message"><?= htmlspecialchars($_SESSION['success']) ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <form action="banias-login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="btn">Verified</button>

            
            
            <?php if (isset($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>