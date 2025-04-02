<?php
// Include database connection
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feature Cards</title>
    <link rel="stylesheet" href="styles.css">
    <script defer src="scripts.js"></script>
</head>
<body>
    <div class="container">
        <div class="card" onclick="window.location.href='view_evaluations.php'">
            <div class="card-container">
                <span class="badge">1</span>
            </div>
            <h3>Intern's Data</h3>
            <p>Detailed performance breakdown (attendance, task completion, feedback, skills, etc.).</p>
            <img src="https://via.placeholder.com/150" alt="Feature Image">
        </div>
        
        <div class="card" onclick="window.location.href='none'">
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
