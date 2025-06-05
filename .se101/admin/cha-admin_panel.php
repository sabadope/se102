<?php
require_once 'cha-auth_check.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: cha-unauthorized.php");
    exit();
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Feedback System</title>
    <link rel="stylesheet" href="cha-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    
    
    <div class="admin-container">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <div class="admin-actions">
                <a href="cha-export_reports.php" class="admin-btn primary">
                    <i class="icon">📊</i> Export All Reports
                </a>
            </div>
        </div>

        <div class="feedback-section">
            <div class="section-header">
                <h2>Supervisor Feedback</h2>
                <span class="badge"><?= $supervisor_result->num_rows ?> records</span>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
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
                            <td class="rating-cell"><?= str_repeat('★', $row['work_quality']) . str_repeat('☆', 5 - $row['work_quality']) ?></td>
                            <td class="rating-cell"><?= str_repeat('★', $row['communication']) . str_repeat('☆', 5 - $row['communication']) ?></td>
                            <td class="rating-cell"><?= str_repeat('★', $row['professionalism']) . str_repeat('☆', 5 - $row['professionalism']) ?></td>
                            <td class="comments-cell"><?= htmlspecialchars($row['comments']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="feedback-section">
            <div class="section-header">
                <h2>Customer Feedback</h2>
                <span class="badge"><?= $customer_result->num_rows ?> records</span>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
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
                            <td class="rating-cell"><?= str_repeat('★', $row['professionalism']) . str_repeat('☆', 5 - $row['professionalism']) ?></td>
                            <td class="rating-cell"><?= str_repeat('★', $row['communication']) . str_repeat('☆', 5 - $row['communication']) ?></td>
                            <td class="rating-cell"><?= str_repeat('★', $row['service_quality']) . str_repeat('☆', 5 - $row['service_quality']) ?></td>
                            <td class="comments-cell"><?= htmlspecialchars($row['comments']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <?php $conn->close(); ?>
</body>
</html>