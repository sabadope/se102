<?php
$host = 'localhost';
$db = 'feedback_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $conn->real_escape_string($_POST['role']);

    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email already registered";
    } else {
        // Insert new user
        $insert_sql = "INSERT INTO users (first_name, last_name, email, password, role) 
                       VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $role);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // If intern, insert into interns table first
            if ($role === 'intern') {
                $intern_sql = "INSERT INTO interns (user_id, department) VALUES (?, 'Pending Assignment')";
                $intern_stmt = $conn->prepare($intern_sql);
                $intern_stmt->bind_param("i", $user_id);

                if ($intern_stmt->execute()) {
                    $intern_stmt->close();
                    header("Location: cha-intern_dashboard.php");
                    exit(); // Prevent further execution
                } else {
                    $error = "Error inserting intern data: " . $intern_stmt->error;
                }
            } else {
                // Redirect non-interns (if needed in future)
                header("Location: cha-intern_dashboard.php");
                exit(); // Prevent further execution
            }
        } else {
            $error = "Error creating user: " . $stmt->error;
        }
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
    <title>User Registration</title>
    <link rel="stylesheet" href="cha-styles.css">

    <style>
        
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


        p {
            text-align: center;
            margin-top: 8px;
        }

        h2 {
            font-size: 2rem;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 500px;">
        <h2>User Authentication</h2>
        
        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>

        <form action="cha-register.php" method="POST">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>

                    <option value="customer" selected>Customer</option>
                </select>
            </div>


            <input type="submit" value="Create Another" style="width: 100%; margin-top: 10px;">
        </form>

                
        </form>
        <p>Already have an account? <a href="cha-login.php" style="font-size: 15.8px; text-decoration: underline;">Verified</a></p>
        
    </div>
</body>
</html