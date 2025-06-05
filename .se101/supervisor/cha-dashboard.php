<?php
require_once 'cha-auth_check.php'; // Ensure the user is authenticated

// Database connection
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch user-specific data (this is a placeholder query)
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="cha-styles.css">
</head>
<body>
    <?php include 'cha-navbar.php'; ?>
    
    <div class="container">
        <h1>Welcome, <?= htmlspecialchars($user['first_name']) ?>!</h1>
        <p>Your role: <?= htmlspecialchars($user['role']) ?></p>
        
        <div class="dashboard-content">
            <h2>Your Information</h2>
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <!-- Add more user-specific information as needed -->
        </div>
    </div>

    
    <?php $conn->close(); ?>
</body>
</html>