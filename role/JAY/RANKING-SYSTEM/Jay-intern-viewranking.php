<?php
// Include the database connection
include 'db_connect.php';

// Initialize message variable
$message = '';

// Ensure intern is logged in (for example, using session-based login)
session_start();

// For now, assume a static intern ID for testing
$intern_id = 1;  // Example intern_id; replace with actual logic for logged-in user
$query = "SELECT id, name, score, task_completion, attendance, feedback 
          FROM rankings WHERE id = ? ORDER BY score DESC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(1, $intern_id, PDO::PARAM_INT);
$stmt->execute();

$intern_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$intern_data) {
    $message = "Intern data not found!";
}

// Fetch the top rankings for comparison
$top_rankings = $pdo->query("SELECT id, name, score FROM rankings ORDER BY score DESC LIMIT 10");
$top_performers = $top_rankings->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Performance Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Add styles here if needed */
    </style>
</head>
<body>
    <div class="container">
        <h2>Intern Performance Dashboard</h2>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Intern Performance Score Section -->
        <h3>Your Performance Score</h3>
        <?php if ($intern_data): ?>
            <div class="performance">
                <p><strong>Name:</strong> <?= htmlspecialchars($intern_data['name']) ?></p>
                <p><strong>Current Performance Score:</strong> <?= number_format($intern_data['score'], 2) ?> pts</p>
                <p><strong>Task Completion:</strong> <?= $intern_data['task_completion'] ?>%</p>
                <p><strong>Attendance:</strong> <?= $intern_data['attendance'] ?>%</p>
                <p><strong>Feedback:</strong> <?= htmlspecialchars($intern_data['feedback']) ?></p>
            </div>
        <?php else: ?>
            <p>Your performance information is not available.</p>
        <?php endif; ?>

        <!-- Ranking Section -->
        <h3>Your Ranking</h3>
        <div class="ranking">
            <p><strong>Your Rank: </strong> 
                <?php 
                // Find rank
                $rank = 1;
                foreach ($top_performers as $performer) {
                    if ($performer['id'] == $intern_id) {
                        break;
                    }
                    $rank++;
                }
                echo $rank;
                ?>
            </p>

            <h4>Top Performers:</h4>
            <ul class="ranking-list">
                <?php foreach ($top_performers as $index => $performer): ?>
                    <li>
                        <?= ($index + 1) . ". " . htmlspecialchars($performer['name']) . " - " . number_format($performer['score'], 2) . " pts" ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Suggestions for Improvement -->
        <h3>Areas for Improvement & Learning Suggestions</h3>
        <div class="suggestions">
            <p>💡 Based on your current performance, here are some recommendations for improvement:</p>
            <ul>
                <li>Focus on increasing your task completion rate.</li>
                <li>Attend more meetings to improve your attendance score.</li>
                <li>Review feedback from your supervisor and make adjustments accordingly.</li>
            </ul>
            <p>Keep up the good work and continue learning!</p>
        </div>
    </div>
</body>
</html>
