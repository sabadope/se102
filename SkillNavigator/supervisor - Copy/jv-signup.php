<?php
// Include database connection
include 'jv-db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = $_POST['role'];

    // Check if the username already exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        echo "<div class='error-message'>Username already exists. Please choose another one.</div>";
    } else {
        // Insert the user into the database
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $password, $role]);

        echo "<div class='success-message'>User registered successfully! <a href='jv-login.php'>Login Now</a></div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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

        /* SignUp Container */
        .signup-container {
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
        input[type="text"], input[type="password"], select {
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
        input[type="text"]:focus, input[type="password"]:focus, select:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.6);
        }

        /* Submit Button */
        input[type="submit"] {
            width: 100%;
            padding: 15px;
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

        /* Success and Error Messages */
        .success-message {
            color: green;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 500;
        }

        .error-message {
            color: red;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 500;
        }

        /* Forgot Password Link */
        p a {
            text-decoration: none;
            color: #007BFF;
            font-size: 14px;
            font-weight: 500;
        }

        /* Hover Effect on Link */
        p a:hover {
            text-decoration: underline;
        }

        /* Media Query for Mobile Devices */
        @media (max-width: 480px) {
            .signup-container {
                padding: 30px;
                width: 90%;
            }

            h2 {
                font-size: 20px;
            }

            input[type="text"], input[type="password"], select, input[type="submit"] {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <form method="POST" action="jv-signup.php">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <select name="role" required>
                <option value="intern">Intern</option>
                <option value="supervisor">Supervisor</option>
            </select><br>
            <input type="submit" value="Sign Up">
        </form>
        <p>Already have an account? <a href="jv-login.php">Login here</a></p>
    </div>
</body>
</html>
