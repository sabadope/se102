<?php
include '../db_connect.php';

// Handle score submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name"]) && isset($_POST["score"])) {
    $name = $conn->real_escape_string($_POST["name"]);
    $score = (int) $_POST["score"];

    $conn->query("INSERT INTO rankings (name, score) VALUES ('$name', $score)");
    header("Location: ranking_system.php"); // Refresh page after submission
    exit();
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"])) {
    $delete_id = (int) $_POST["delete_id"];
    $conn->query("DELETE FROM rankings WHERE id = $delete_id");
    header("Location: ranking_system.php"); // Refresh page after deletion
    exit();
}

// Fetch rankings
$query = "SELECT id, name, score FROM rankings ORDER BY score DESC LIMIT 10";
$result = $conn->query($query);

// Prepare data for chart
$names = [];
$scores = [];
$ids = [];

while ($row = $result->fetch_assoc()) {
    $ids[] = $row['id'];
    $names[] = $row['name'];
    $scores[] = $row['score'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Score & Ranking System</title>
    <link rel="stylesheet" href="../static/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav>
        <ul>
            <li><a href="base.php">Home</a></li>
            <li><a href="task_tracker.php">Task Tracker</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <h2>Performance Score & Ranking System</h2>

        <!-- Form to Add Scores -->
        <form method="POST">
            <input type="text" name="name" placeholder="Enter name" required>
            <input type="number" name="score" placeholder="Enter score" required>
            <button type="submit">Submit Score</button>
        </form>

        <!-- Ranking List -->
        <ul class="ranking">
            <?php if (!empty($names)): ?>
                <?php foreach ($names as $index => $name): ?>
                    <li>
                        <span class="rank-number"><?= ($index + 1) . "."; ?></span>
                        <?= htmlspecialchars($name) . " - " . $scores[$index] . " pts"; ?>
                        
                        <!-- Delete Button Form -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $ids[$index]; ?>">
                            <button type="submit" class="delete-btn">Remove</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No rankings available.</li>
            <?php endif; ?>
        </ul>

        <!-- Ranking Chart -->
        <div class="chart-container">
            <canvas id="rankingChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('rankingChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($names) ?>,
                    datasets: [{
                        label: 'Scores',
                        data: <?= json_encode($scores) ?>,
                        backgroundColor: '#007bff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
</body>
</html>
