<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "intern_logs");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch saved logs
$sql = "SELECT * FROM logs ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Performance Logs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Intern Performance Logs</h1>
        <p>Update your daily and weekly tasks here.</p>

        <!-- Tabs for Daily and Weekly Logs -->
        <div class="tabs">
            <button class="tab-button active" onclick="openTab('daily')">Daily Log</button>
            <button class="tab-button" onclick="openTab('weekly')">Weekly Log</button>
        </div>

        <!-- Daily Log Form -->
        <form action="save_log.php" method="POST" id="daily-log-form" class="tab-content active">
            <input type="hidden" name="type" value="Daily Log">
            <div class="form-group">
                <label>Task Name:</label>
                <input type="text" name="task_name" required>
            </div>
            <div class="form-group">
                <label>Task Description:</label>
                <textarea name="task_desc" required></textarea>
            </div>
            <div class="form-group">
                <label>Start Time:</label>
                <input type="time" name="start_time" required>
            </div>
            <div class="form-group">
                <label>End Time:</label>
                <input type="time" name="end_time" required>
            </div>
            <div class="form-group">
                <label>Status:</label>
                <select name="status" required>
                    <option>Completed</option>
                    <option>In Progress</option>
                    <option>Pending</option>
                </select>
            </div>
            <button type="submit">Save Daily Log</button>

        </form>

        <!-- Weekly Log Form -->
        <form action="save_log.php" method="POST" id="weekly-log-form" class="tab-content">
            <input type="hidden" name="type" value="Weekly Log">
            <div class="form-group">
                <label>Weekly Goals:</label>
                <textarea name="weekly_goals" required></textarea>
            </div>
            <div class="form-group">
                <label>Achievements:</label>
                <textarea name="achievements" required></textarea>
            </div>
            <div class="form-group">
                <label>Challenges:</label>
                <textarea name="challenges" required></textarea>
            </div>
            <div class="form-group">
                <label>Lessons Learned:</label>
                <textarea name="lessons" required></textarea>
            </div>
            <button type="submit">Save Weekly Log</button>
        </form>

        <!-- Saved Logs -->
        <h2>Saved Logs</h2>
        <button onclick="window.location.href='export_logs.php'" class="export-btn">📤 Export Logs</button>
        <div id="saved-logs">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="log-entry">
                    <h3><?= $row['type'] ?> - <?= $row['timestamp'] ?></h3>
                    <?= $row['task_name'] ? "<p><strong>Task:</strong> {$row['task_name']}</p>" : "" ?>
                    <?= $row['weekly_goals'] ? "<p><strong>Weekly Goals:</strong> {$row['weekly_goals']}</p>" : "" ?>
                    <button onclick="deleteLog(<?= $row['id'] ?>)">🗑 Delete</button>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>