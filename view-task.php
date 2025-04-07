<?php
// Include database connection
require 'db_connect.php';

// Fetch tasks from database (including file_path)
$sql = "SELECT * FROM tasks ORDER BY created_on DESC";
$result = $conn->query($sql);

// Check for logout request
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
    <title>View Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar min-vh-100 bg-dark text-white p-3">
                <a href="supervisor-view.php" class="d-block text-white py-2">Task Management</a>
                <a href="assign-task.php" class="d-block text-white py-2">Assign Task</a>
                <a href="view-task.php" class="d-block text-white py-2">View Task</a>
                <a href="?logout=1" class="d-block text-danger py-2">Log out</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2>Task Report</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr.no.</th>
                                <th>Assigned To</th>
                                <th>Company Name</th>
                                <th>Task Name</th>
                                <th>Created on</th>
                                <th>Start Date</th>
                                <th>Start Time</th>
                                <th>End Date</th>
                                <th>End Time</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                <th>Rating</th>
                                <th>Feedback</th>
                                <th>Completion Time</th>
                                <th>Uploaded File</th> <!-- New column for the file -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($result->num_rows > 0) {
                                $sr_no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $sr_no++ . "</td>";
                                    echo "<td>" . htmlspecialchars($row['assigned_by'] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['company_name'] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['task_name'] ?? 'N/A') . "</td>";
                                    echo "<td>" . (isset($row['created_on']) ? date('m-d-Y',) : 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['start_date'] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['start_time'] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['end_date'] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['end_time'] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['status'] ?? 'N/A') . "</td>";
                                    echo "<td>" . (isset($row['deadline']) ? date('m-d-Y', ) : 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['rating'] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['feedback'] ?? 'No feedback') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['completion_time'] ?? 'N/A') . " minutes</td>";
                                    
                                    // Check if file_path exists and display a link
                                    $file_path = $row['file_path'] ?? null;
                                    if ($file_path) {
                                        echo "<td><a href='$file_path' target='_blank'>View File</a></td>";
                                    } else {
                                        echo "<td>No File Uploaded</td>";
                                    }

                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='15' class='text-center'>No tasks available</td></tr>";
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
