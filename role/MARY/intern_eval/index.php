<?php
session_start();

// Check if logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Include database connection
include 'db_connect.php';

// Redirect new interns with missing info to interns_log.php
if ($role === 'intern') {
    $check = $conn->query("SELECT name, attendance, tasks_completed FROM interns WHERE user_id = '$username'");

    if ($check->num_rows === 0) {
        header("Location: interns_log.php");
        exit;
    } else {
        $row = $check->fetch_assoc();
        if (empty($row['name']) || $row['attendance'] === null || $row['tasks_completed'] === null) {
            header("Location: interns_log.php");
            exit;
        }
    }
}

// Format name display
$roleFormatted = ucfirst($role);
$usernameDisplay = htmlspecialchars($username);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
    <script defer src="scripts.js"></script>
</head>
<body>

 <!-- Logout Button -->
 <div class="logout-container"> 
        <a href="logout.php" class="logout-btn">Logout</a>
 </div>

 <div class="welcome-phrase">
 <p>Welcome, <?php echo ucfirst($_SESSION['role']) . ' ' . htmlspecialchars($_SESSION['username']); ?>!</p>

 </div>

    <div class="container">
        <div class="card" onclick="window.location.href='view_evaluations.php'">
            <div class="card-container">
                <span class="badge">1</span>
            </div>
            <h3>Intern's Data</h3>
            <p>Detailed performance breakdown (attendance, task completion, feedback, skills, etc.).</p>
            <img src="https://via.placeholder.com/150" alt="Feature Image">
        </div>
        
        <div class="card" onclick="window.location.href='hiring_reco.php'">
            <div class="card-container">
                <span class="badge">2</span>
            </div>
            <h3>Intern’s Score and Ranking</h3>
            <p>Hiring recommendation based on system-generated analysis.</p>
            <img src="https://via.placeholder.com/150" alt="Feature Image">
        </div>
    </div>
</body>
</html>
