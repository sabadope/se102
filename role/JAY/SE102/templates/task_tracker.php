<?php
include '../db_connect.php';

// Handle task submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["task_name"])) {
    $task_name = $conn->real_escape_string($_POST["task_name"]);
    $conn->query("INSERT INTO tasks (task_name, status) VALUES ('$task_name', 'pending')");
}

// Handle task completion
if (isset($_GET["complete"])) {
    $task_id = (int) $_GET["complete"];
    $conn->query("UPDATE tasks SET status='completed' WHERE id=$task_id");
}

// Fetch tasks
$result = $conn->query("SELECT * FROM tasks");

// Fetch task count for graph
$pending_count = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
$completed_count = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status='completed'")->fetch_assoc()['total'] ?? 0;
$in_progress_count = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status='in progress'")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Tracker</title>
    <link rel="stylesheet" href="../static/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav>
        <ul>
            <li><a href="base.php">Home</a></li>
            <li><a href="ranking_system.php">Ranking System</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Task Completion & Efficiency Tracker</h2>

        <!-- Task Form -->
        <form method="POST">
            <input type="text" name="task_name" placeholder="Enter new task" required>
            <button type="submit">Add Task</button>
        </form>

        <!-- Task Table -->
        <table>
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['task_name']) ?></td>
                        <td class="<?= strtolower(str_replace(' ', '_', $row['status'])) ?>">
                            <?php 
                                // Normalize status to ensure consistency (convert to lowercase and trim spaces)
                                $status = strtolower(trim($row['status']));  

                                // Define the icons for each status
                                $status_icons = [
                                    'pending' => '⏳ Pending',
                                    'in progress' => '🔄 In Progress',
                                    'completed' => '✅ Completed'
                                ];

                                // Display the appropriate status icon, or show '❓ Unknown' if not found
                                echo $status_icons[$status] ?? '❓ Unknown';
                            ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <a href="?complete=<?= $row['id'] ?>" class="complete-btn">✔ Mark Complete</a>
                            <?php elseif ($row['status'] == 'in progress'): ?>
                                <span class="in-progress">🔄 In Progress</span>
                            <?php else: ?>
                                <span class="done">✅</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Task Graph -->
        <div class="chart-container">
            <canvas id="taskChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('taskChart').getContext('2d');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Pending', 'In Progress'],
                    datasets: [{
                        data: [<?= $completed_count ?>, <?= $pending_count ?>, <?= $in_progress_count ?>],
                        backgroundColor: ['#4CAF50', '#FF9800', '#2196F3']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 0 }
                }
            });
        });
    </script>
</body>
</html>
