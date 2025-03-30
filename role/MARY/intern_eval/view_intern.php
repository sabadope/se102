<?php
// Include database connection
include 'db_connect.php';

// Check if ID is set
if (isset($_GET['id'])) {
    $intern_id = $_GET['id'];

    // Fetch intern data
    $query = "SELECT * FROM interns WHERE intern_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $intern_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $intern = $result->fetch_assoc();
    } else {
        echo "No intern found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Intern Report</title>
    <link rel="stylesheet" href="view_intern.css">
</head>
<body>
<div class="containers">
    <!-- Header and Search Bar Wrapper -->
    <div class="header-container">
        <h2>Intern's Full Report</h2>
    </div>
</div>

<table border="1">
    <tr><th>ID</th><td><?php echo $intern['intern_id']; ?></td></tr>
    <tr><th>Name</th><td><?php echo $intern['name']; ?></td></tr>
    <tr><th>Attendance</th><td><?php echo $intern['attendance']; ?>%</td></tr>
    <tr><th>Tasks Completed</th><td><?php echo $intern['tasks_completed']; ?></td></tr>
    <tr><th>Feedback</th><td><?php echo $intern['feedback']; ?></td></tr>
    <tr><th>Skills</th><td><?php echo $intern['skills']; ?></td></tr>
    <tr><th>Overall Score</th><td><?php echo $intern['overall_score']; ?></td></tr>
    <tr><th>Ranking</th><td><?php echo $intern['ranking']; ?></td></tr>
</table>

<a href="view_evaluations.php" class="back-btn">Back</a>

</body>
</html>
