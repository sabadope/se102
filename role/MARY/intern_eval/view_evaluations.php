<?php
// Include database connection
include 'db_connect.php';

// Initialize search variable
$search = "";
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

if ($search) {
    // If searching, show all interns that match ID, name, or rank
    $result = $conn->query("SELECT * FROM interns WHERE 
        intern_id LIKE '%$search%' OR 
        name LIKE '%$search%' OR 
        ranking LIKE '%$search%'
    ");
} else {
    // Default: Show only top 3 ranked interns
    $result = $conn->query("SELECT * FROM interns WHERE ranking IN (1, 2, 3) ORDER BY ranking ASC");
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Intern Evaluations</title>
    <link rel="stylesheet" href="viewEval.css">
</head>
<body>

<div class="containers">
    <!-- Header and Search Bar Wrapper -->
    <div class="header-container">
        <h2>Interns Data</h2>
        <!-- Search Form -->
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search by ID or Name" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        </div>
    </div>
</div>


<?php
// Check if any results are found
if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Attendance</th>
                <th>Tasks Completed</th>
                <th>Feedback</th>
                <th>Skills</th>
                <th>Overall Score</th>
                <th>Ranking</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['intern_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['attendance']}%</td>
                <td>{$row['tasks_completed']}</td>
                <td>{$row['feedback']}</td>
                <td>{$row['skills']}</td>
                <td>{$row['overall_score']}</td>
                <td>{$row['ranking']}</td>
                <td>
                <a href='view_intern.php?id={$row['intern_id']}' class='view-btn'>View</a>
            </td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "No intern records found.";
}

$conn->close();
?>

</body>
</html>
