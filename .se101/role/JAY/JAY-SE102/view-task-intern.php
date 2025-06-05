<?php
// Include database connection
require 'db_connect.php';

// Optional: Start session if intern login is implemented
// session_start();
// $intern_id = $_SESSION['user_id']; // Adjust if you're using login system

// Fetch tasks assigned to the intern
$sql = "SELECT * FROM tasks ORDER BY created_on DESC";
$result = $conn->query($sql);

// Logout logic
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_start();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View My Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar min-vh-100 bg-dark text-white p-3">
                <a href="intern-view.php" class="d-block text-white py-2">Dashboard</a>
                <a href="view-task-intern.php" class="d-block text-white py-2">View Tasks</a>
                <a href="?logout=1" class="d-block text-danger py-2">Log out</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2>My Assigned Tasks</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr.no.</th>
                                <th>Task Name</th>
                                <th>Assigned By</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Rating</th>
                                <th>Feedback</th>
                                <th>Completion Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($result->num_rows > 0) {
                                $sr_no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $sr_no++ . "</td>";
                                    echo "<td>" . htmlspecialchars($row['task_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['assigned_by']) . "</td>";
                                    echo "<td>" . $deadline = isset($row['deadline']) ? date('m-d-Y') : 'N/A';
                                    echo "<td class='text-capitalize'>" . htmlspecialchars($row['status']) . "</td>";
                                    echo "<td>" . ($row['rating'] !== null ? $row['rating'] : 'N/A') . "</td>";
                                    echo "<td>" . (!empty($row['feedback']) ? htmlspecialchars($row['feedback']) : 'No Feedback') . "</td>";
                                    echo "<td>" . ($row['completion_time'] ?? 'N/A') . " mins</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No tasks assigned to you.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
