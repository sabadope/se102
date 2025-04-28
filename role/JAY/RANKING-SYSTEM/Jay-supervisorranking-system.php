<?php
include 'db_connect.php';

// Initialize message variable
$message = '';

// Handle score submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    if ($_POST["action"] === "add_score") {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        
        // Ensure that task_completion, quality, and timeliness are integers
        $task_completion = (int) filter_input(INPUT_POST, 'task_completion', FILTER_VALIDATE_INT);
        $quality = (int) filter_input(INPUT_POST, 'quality', FILTER_VALIDATE_INT);
        $timeliness = (int) filter_input(INPUT_POST, 'timeliness', FILTER_VALIDATE_INT);
        $feedback = filter_input(INPUT_POST, 'feedback', FILTER_SANITIZE_STRING);

        // Check if the variables are valid
        if ($task_completion !== false && $quality !== false && $timeliness !== false) {
            // Calculate weighted score (example weights: 40% task completion, 30% quality, 30% timeliness)
            $score = ($task_completion * 0.3) + ($quality * 0.4) + ($timeliness * 0.3);

            if ($name && $score !== false) {
                $stmt = $pdo->prepare("INSERT INTO rankings (name, score, task_completion, quality, timeliness, feedback) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bindParam(1, $name);
                $stmt->bindParam(2, $score);
                $stmt->bindParam(3, $task_completion, PDO::PARAM_INT);
                $stmt->bindParam(4, $quality, PDO::PARAM_INT);
                $stmt->bindParam(5, $timeliness, PDO::PARAM_INT);
                $stmt->bindParam(6, $feedback);

                if ($stmt->execute()) {
                    $message = "Score added successfully!";
                } else {
                    $message = "Error adding score.";
                }
            } else {
                $message = "Invalid input data!";
            }
        } else {
            $message = "Invalid input data!";
        }
    }
}

    // Handle delete request
    if (isset($_POST["action"]) && $_POST["action"] === "delete" && isset($_POST["delete_id"])) {
        // Sanitize and validate delete_id
        $delete_id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
    
        // Check if delete_id is a valid integer
        if ($delete_id) {
            // Prepare DELETE statement
            $stmt = $pdo->prepare("DELETE FROM rankings WHERE id = ?");
            $stmt->bindParam(1, $delete_id, PDO::PARAM_INT);
    
            // Execute the DELETE query
            if ($stmt->execute()) {
                $message = "Entry deleted successfully!";
            } else {
                $message = "Error deleting entry.";
            }
        } else {
            // Handle invalid delete_id
            $message = "Invalid entry ID.";
        }
    } else {
        // Handle case where action or delete_id are not set
        $message = "Invalid request.";
    }
    

   // Check if the 'action' key exists in the POST request
if (isset($_POST["action"]) && $_POST["action"] === "export") {
    // Prepare the export query
    $stmt = $pdo->query("SELECT name, score, task_completion, attendance, skill_growth, feedback 
        FROM rankings ORDER BY score DESC");

    // Set headers for the CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="performance_report.csv"');

    // Open output stream to the browser
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Score', 'Task Completion', 'Attendance', 'Skill Growth', 'Feedback']);

    // Fetch and write data to CSV
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['name'],
            $row['score'],
            $row['task_completion'],
            $row['attendance'],
            $row['skill_growth'],
            $row['feedback']
        ]);
    }

    // Close the output stream
    fclose($output);
    exit();
}


// Fetch rankings with additional fields
$query = "SELECT id, name, score, task_completion, quality, timeliness, attendance, feedback 
    FROM rankings ORDER BY score DESC LIMIT 10";
$stmt = $pdo->query($query);

// Prepare data for chart and display
$names = [];
$scores = [];
$ids = [];
$details = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $ids[] = $row['id'];
    $names[] = $row['name'];
    $scores[] = $row['score'];
    $details[] = [
        'task_completion' => $row['task_completion'],
        'quality' => $row['quality'],
        'timeliness' => $row['timeliness'],
        'attendance' => $row['attendance'],  // Replace skill_growth with attendance or another valid column
        'feedback' => $row['feedback']
    ];    
}

// Database schema (run this once to update the table structure)
$schema_query = "CREATE TABLE IF NOT EXISTS rankings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    score DOUBLE NOT NULL,
    task_completion INT NOT NULL CHECK (task_completion BETWEEN 0 AND 100),
    attendance INT NOT NULL CHECK (attendance BETWEEN 0 AND 100),
    skill_growth INT NOT NULL CHECK (skill_growth BETWEEN 0 AND 100),
    feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$pdo->exec($schema_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Score & Ranking System</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .top-performer { background-color: #ffd700; padding: 5px; border-radius: 3px; }
        .feedback { font-style: italic; color: #666; }
        .criteria-inputs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .message { padding: 10px; margin: 10px 0; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    
    <div class="container">
        <h2>Performance Score & Ranking System</h2>

        <!-- Display Messages -->
        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Form to Add Scores -->
        <form method="POST">
            <input type="hidden" name="action" value="add_score">
            <div class="form-group">
                <input type="text" name="name" placeholder="Enter name" required>
            </div>
            <div class="criteria-inputs">
                <div>
                    <label>Task Completion (0-100):</label>
                    <input type="number" name="task_completion" min="0" max="100" required>
                </div>
                <div>
                    <label>Attendance Score (0-100):</label>
                    <input type="number" name="timeliness" min="0" max="100" required>
                </div>
                <div>
                    <label>Skill Growth (0-100):</label>
                    <input type="number" name="quality" min="0" max="100" required>
                </div>
            </div>
            <div class="form-group">
                <textarea name="feedback" placeholder="Performance feedback" rows="4"></textarea>
            </div>
            <button type="submit">Submit Score</button>
        </form>

        <!-- Export Button -->
        <form method="POST">
            <input type="hidden" name="action" value="export">
            <button type="submit">Export to CSV</button>
        </form>

        <!-- Ranking List -->
        <h3>Top Performers</h3>
        <ul class="ranking">
            <?php if (!empty($names)): ?>
                <?php foreach ($names as $index => $name): ?>
                    <li class="<?= $index < 3 ? 'top-performer' : '' ?>">
                        <span class="rank-number"><?= ($index + 1) . "."; ?></span>
                        <?= htmlspecialchars($name) . " - " . number_format($scores[$index], 2) . " pts"; ?>
                        <div class="details">
                            <small>
                                Task: <?= $details[$index]['task_completion'] ?> |
                                Attendance: <?= $details[$index]['timeliness'] ?> |
                                Skill Growth: <?= $details[$index]['quality'] ?>
                            </small>
                            <?php if ($details[$index]['feedback']): ?>
                                <div class="feedback">
                                    Feedback: <?= htmlspecialchars($details[$index]['feedback']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Delete Button Form -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
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
                        label: 'Performance Scores',
                        data: <?= json_encode($scores) ?>,
                        backgroundColor: '#007bff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { 
                            beginAtZero: true,
                            title: { display: true, text: 'Score' }
                        },
                        x: { title: { display: true, text: 'Interns' } }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Intern Performance Rankings'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
