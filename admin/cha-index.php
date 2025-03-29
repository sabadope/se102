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
    <style>
        
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.05); /* Subtle shadow */
            border-radius: 12px;
            border: 1px solid #e5e7eb; /* Light gray border */
        }

        h1 {
            color: #1e40af; /* Deep blue for headings */
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
        }

        p {
            text-align: center;
            color: #6b7280; /* Gray for subtext */
            margin-bottom: 20px;
            font-size: 16px;
        }

        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .tab-button {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            background: #e5e7eb; /* Light gray for inactive tabs */
            color: #6b7280; /* Gray text */
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            background: #1e40af; /* Deep blue for active tab */
            color: white;
        }

        .tab-button:hover {
            background: #3b82f6; /* Lighter blue on hover */
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: 600;
            color: #1e40af; /* Deep blue for labels */
            margin-bottom: 5px;
            display: block;
            font-size: 14px;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #e5e7eb; /* Light gray border */
            border-radius: 6px;
            font-size: 14px;
            background: #f9fafb; /* Light gray background for inputs */
            color: #333; /* Dark gray text */
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        button {
            margin-top: 20px;
            background: #1e40af; /* Deep blue for buttons */
            color: white;
            border: none;
            padding: 12px;
            cursor: pointer;
            border-radius: 6px;
            font-size: 16px;
            transition: background 0.3s ease;
            width: 100%;
        }

        button:hover {
            background: #3b82f6; /* Lighter blue on hover */
        }

        #saved-logs {
            margin-top: 30px;
            padding: 15px;
            background: #f9fafb; /* Light gray background */
            border-radius: 6px;
            border: 1px solid #e5e7eb; /* Light gray border */
        }

        .log-entry {
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border: 1px solid #e5e7eb; /* Light gray border */
            border-radius: 6px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05); /* Subtle shadow */
        }

        .log-entry h3 {
            color: #1e40af; /* Deep blue for log titles */
            margin-bottom: 10px;
            font-size: 18px;
        }

        .log-entry p {
            margin: 5px 0;
            color: #6b7280; /* Gray for log content */
            font-size: 14px;
        }

        .log-entry button {
            margin-top: 10px;
            background: #ef4444; /* Red for delete button */
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            width: auto;
        }

        .log-entry button:hover {
            background: #dc2626; /* Darker red on hover */
        }

        .export-btn {
            background: #1e40af;
            color: white;
            border: none;
            padding: 12px;
            cursor: pointer;
            border-radius: 6px;
            font-size: 16px;
            transition: background 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        .export-btn:hover {
            background: #3b82f6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #1e40af; 
            color: white;
            font-weight: bold;
        }

        td {
            background: #f9fafb; 
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        form textarea, form select, form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form button {
            background: #1e40af;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        form button:hover {
            background: #3b82f6;
        }

    </style>
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
            <button onclick="window.location.href='weekly_summary.php'" style="margin-bottom: 20px; background: #1e40af; color: white; border: none; padding: 12px; cursor: pointer; border-radius: 6px; font-size: 16px; transition: background 0.3s ease; width: 100%;">📊 View Weekly Summary</button>

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
    <script>
        
        function openTab(tabName) {
            document.querySelectorAll(".tab-content").forEach(tab => tab.classList.remove("active"));
            document.querySelectorAll(".tab-button").forEach(button => button.classList.remove("active"));
            document.getElementById(`${tabName}-log-form`).classList.add("active");
            document.querySelector(`button[onclick="openTab('${tabName}')"]`).classList.add("active");
        }

        function deleteLog(id) {
            if (confirm("Are you sure you want to delete this log?")) {
                window.location.href = `delete_log.php?id=${id}`;
            }
        }
    </script>
</body>
</html>