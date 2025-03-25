<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
    header("Location: login.php");
    exit();
}

// Connect to database
$host = 'localhost';
$db = 'feedback_system';
$user = 'root';
$pass = '';  // Replace with your DB password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch supervisor feedback
$supervisor_query = "SELECT sf.id, u.first_name AS intern_name, s.first_name AS supervisor_name, 
                        sf.work_quality, sf.communication, sf.professionalism, sf.comments, sf.created_at
                     FROM supervisor_feedback sf
                     JOIN interns i ON sf.intern_id = i.id
                     JOIN users u ON i.user_id = u.id
                     JOIN users s ON sf.supervisor_id = s.id
                     ORDER BY sf.created_at DESC";
$supervisor_result = $conn->query($supervisor_query);

// Fetch customer feedback
$customer_query = "SELECT cf.id, u.first_name AS intern_name, c.first_name AS customer_name, 
                        cf.professionalism, cf.communication, cf.service_quality, cf.comments, cf.created_at
                   FROM customer_feedback cf
                   JOIN interns i ON cf.intern_id = i.id
                   JOIN users u ON i.user_id = u.id
                   JOIN users c ON cf.customer_id = c.id
                   ORDER BY cf.created_at DESC";
$customer_result = $conn->query($customer_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Feedback System</title>
    <link rel="stylesheet" href="styles.css">  <!-- Link to CSS -->
</head>
<body>
    <h1>Admin Panel</h1>

    <h2>Supervisor Feedback</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Intern</th>
                <th>Supervisor</th>
                <th>Work Quality</th>
                <th>Communication</th>
                <th>Professionalism</th>
                <th>Comments</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $supervisor_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['intern_name']) ?></td>
                <td><?= htmlspecialchars($row['supervisor_name']) ?></td>
                <td><?= $row['work_quality'] ?></td>
                <td><?= $row['communication'] ?></td>
                <td><?= $row['professionalism'] ?></td>
                <td><?= htmlspecialchars($row['comments']) ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Customer Feedback</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Intern</th>
                <th>Customer</th>
                <th>Professionalism</th>
                <th>Communication</th>
                <th>Service Quality</th>
                <th>Comments</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $customer_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['intern_name']) ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= $row['professionalism'] ?></td>
                <td><?= $row['communication'] ?></td>
                <td><?= $row['service_quality'] ?></td>
                <td><?= htmlspecialchars($row['comments']) ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <br>
    <a href="export_reports.php">Export Reports (CSV)</a>

</body>
</html>

<?php
$conn->close();
?>
