<?php
session_start();
include 'db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Initialize search variable
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Base query
if ($role === 'supervisor') {
    // Supervisor: Can view all
    $sql = "SELECT intern_id, name, overall_score, ranking FROM interns";

    if (!empty($search)) {
        $sql .= " WHERE intern_id LIKE '%$search%' 
                  OR name LIKE '%$search%' 
                  OR ranking LIKE '%$search%'";
    } else {
        $sql .= " WHERE ranking IN (1, 2, 3) ORDER BY ranking ASC";
    }
} else {
    // Intern: Can only view their own record
    $sql = "SELECT intern_id, name, overall_score, ranking 
            FROM interns 
            WHERE user_id = $user_id";
}

// Execute Query
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Intern Evaluations</title>
    <link rel="stylesheet" href="view_eval.css">
</head>
<body>

<div class="containers">
    <div class="header-container">
        <h2>Interns Data</h2>

        <?php if ($role === 'supervisor'): ?>
        <!-- Show search only for supervisors -->
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search by ID or Name" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<div class="back-container">
    <a href="index.php" class="back-btn">← Back</a>
</div>

<?php
// Display results
if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Overall Score</th>
                <th>Ranking</th>
                <th>Action</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['intern_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['overall_score']}</td>
                <td>{$row['ranking']}</td>
                <td><a href='view_intern.php?id={$row['intern_id']}' class='view-button'>View</a></td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No intern records found.</p>";
}

$conn->close();
?>

</body>
</html>
