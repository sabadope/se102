<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Tracker & Ranking System</title>
    <link rel="stylesheet" href="../static/styles.css">

</head>
<body>
    <nav>
        <ul>
            <li><a href="task_tracker.php">Task Tracker</a></li>
            <li><a href="ranking_system.php">Ranking System</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>Welcome to Performance Tracking</h1>
        <p>Select an option from the navigation menu.</p>
        
        <?php
            // Display the current date dynamically
            echo "<p>Today is " . date("l, F j, Y") . ".</p>";
        ?>
    </div>
</body>
</html>
