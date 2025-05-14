<?php
require_once 'cha-auth_check.php';
if ($_SESSION['role'] !== 'intern') {
    header("Location: cha-unauthorized.php");
    exit();
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$intern_id = $_SESSION['user_id'];

// Fetch intern progress data
$progress_query = "SELECT * FROM intern_progress WHERE intern_id = ?";
$stmt = $conn->prepare($progress_query);
$stmt->bind_param("i", $intern_id);
$stmt->execute();
$progress_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Progress</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'cha-navbar.php'; ?>
    
    <div class="container">
        <h1>Intern Progress</h1>
        
        <div class="progress-section">
            <?php if ($progress_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Status</th>
                            <th>Comments</th>
                            <th>Date Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $progress_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['task']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['comments']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['date_updated'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No progress data available.</p>
            <?php endif; ?>
        </div>
    </div>

    
    <?php $conn->close(); ?>
</body>
</html>