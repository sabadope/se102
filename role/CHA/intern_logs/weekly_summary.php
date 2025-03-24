<?php
// Connect to database
include 'db_connect.php'; 

// Get current week date range
$start_date = date('Y-m-d', strtotime('monday this week'));
$end_date = date('Y-m-d', strtotime('sunday this week'));

// Step 1: Identify the date column
$result = $conn->query("SHOW COLUMNS FROM logs");
$date_column = null;
$columns = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
        if (strpos($row['Field'], 'date') !== false) {
            $date_column = $row['Field'];
        }
    }
}

// Fallback if no date column is found
if (!$date_column) {
    die("No date column found in the logs table.");
}

// Step 2: Build the SELECT query dynamically
$selected_columns = [];
$required_fields = ['task', 'time_spent', 'challenges', 'improvements'];

foreach ($required_fields as $field) {
    $matching_column = array_filter($columns, fn($col) => stripos($col, $field) !== false);
    $selected_columns[] = $matching_column ? current($matching_column) . " AS $field" : "NULL AS $field";
}

$query = "
    SELECT 
        $date_column, 
        " . implode(", ", $selected_columns) . " 
    FROM logs 
    WHERE $date_column >= '$start_date' AND $date_column <= '$end_date'
";

$result = $conn->query($query);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weekly Performance Summary</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 0 10px #ccc;
            border-radius: 8px;
        }
        h1, h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background: #333;
            color: #fff;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label, select, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }
        button {
            background: #4CAF50;
            color: #fff;
            cursor: pointer;
            border: none;
            transition: background 0.3s;
        }
        button:hover {
            background: #45a049;
        }
        .tabs {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .tab-button {
            padding: 10px 20px;
            border: none;
            background: #333;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }
        .tab-button:hover {
            background: #555;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Weekly Performance Summary</h1>

    <div class="tabs">
        <button class="tab-button active" onclick="openTab('intern')">Intern View</button>
        <button class="tab-button" onclick="openTab('supervisor')">Supervisor Review</button>
    </div>

    <!-- Intern View -->
    <div id="intern-log-form" class="tab-content active">
        <h2>Intern View</h2>
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Task</th>
                        <th>Time Spent</th>
                        <th>Challenges</th>
                        <th>Improvements</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row[$date_column] ?? 'N/A' ?></td>
                        <td><?= $row['task'] ?? 'No task recorded' ?></td>
                        <td><?= $row['time_spent'] ?? 'N/A' ?></td>
                        <td><?= $row['challenges'] ?? 'No challenges' ?></td>
                        <td><?= $row['improvements'] ?? 'No improvements' ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No logs found for this week.</p>
        <?php endif; ?>
    </div>

    <!-- Supervisor Review -->
    <div id="supervisor-log-form" class="tab-content">
        <h2>Supervisor Review</h2>
        <form action="save_supervisor_review.php" method="POST">
            <div class="form-group">
                <label for="feedback">Feedback:</label>
                <textarea name="feedback" id="feedback" rows="5" required></textarea>
            </div>

            <div class="form-group">
                <label for="rating">Rating:</label>
                <select name="rating" id="rating" required>
                    <option value="Excellent">Excellent</option>
                    <option value="Good">Good</option>
                    <option value="Average">Average</option>
                    <option value="Needs Improvement">Needs Improvement</option>
                </select>
            </div>

            <button type="submit">Save Review</button>
        </form>
    </div>
</div>

<script>
    // Tab functionality
    function openTab(tabName) {
        const tabs = document.querySelectorAll('.tab-content');
        const buttons = document.querySelectorAll('.tab-button');

        tabs.forEach(tab => tab.classList.remove('active'));
        buttons.forEach(btn => btn.classList.remove('active'));

        document.getElementById(`${tabName}-log-form`).classList.add('active');
        event.currentTarget.classList.add('active');
    }
</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
