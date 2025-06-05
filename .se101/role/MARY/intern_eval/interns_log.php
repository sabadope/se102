<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is an intern
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'intern') {
    header("Location: index.php");
    exit;
    
}

$user_id = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $attendance = (int) $_POST['attendance'];
    $tasks_completed = (int) $_POST['tasks_completed'];

    // Check if a record already exists for this user_id
    $check = $conn->query("SELECT * FROM interns WHERE user_id = '$user_id'");

    if ($check->num_rows > 0) {
        // Update existing record
        $sql = "UPDATE interns 
                SET name = '$name', attendance = $attendance, tasks_completed = $tasks_completed 
                WHERE user_id = '$user_id'";
    } else {
        // Insert new record
        $sql = "INSERT INTO interns (name, attendance, tasks_completed, user_id) 
                VALUES ('$name', $attendance, $tasks_completed, '$user_id')";
    }

    if ($conn->query($sql)) {
        $success = "Your data has been saved!";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Intern Info Log</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Your Information</h2>

        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="number" name="attendance" placeholder="Total Attendance" required>
            <input type="number" name="tasks_completed" placeholder="Tasks Completed" required>
            <button type="submit">Save Info</button>
        </form>
    </div>
</body>
</html>
