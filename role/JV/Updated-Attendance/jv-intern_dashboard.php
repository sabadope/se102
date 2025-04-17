<?php
session_start();
include 'jv-db.php'; // Make sure db.php sets $pdo

// Redirect if user is not logged in or not an intern
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'intern') {
    header('Location: jv-login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch intern info
$sqlUser = "SELECT username FROM users WHERE id = ?";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->execute([$user_id]);
$user = $stmtUser->fetch();

// Fetch attendance records
$sql = "SELECT * FROM attendance WHERE user_id = ? ORDER BY date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$attendanceRecords = $stmt->fetchAll();
?>

<!-- Begin HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Dashboard - View Attendance</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(145deg, #e3f2fd, #f1f8e9);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 30px 40px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 1000px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 26px;
            margin-bottom: 25px;
        }

        .info {
            margin-bottom: 20px;
            font-size: 17px;
            color: #444;
            background-color: #f7f9fc;
            padding: 15px;
            border-radius: 10px;
        }

        .info p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
            border-radius: 12px;
            overflow: hidden;
        }

        thead {
            background-color: #3498db;
            color: white;
        }

        th, td {
            padding: 14px;
            text-align: center;
            font-size: 15px;
        }

        tr:nth-child(even) {
            background-color: #f2f6fa;
        }

        tr:hover {
            background-color: #e8f0fe;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .status-present {
            color: #27ae60;
            font-weight: bold;
        }

        .status-late {
            color: #f39c12;
            font-weight: bold;
        }

        .status-absent {
            color: #e74c3c;
            font-weight: bold;
        }

        .logout-btn {
            margin-top: 30px;
            display: inline-block;
            padding: 12px 25px;
            background-color: #dc3545;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        @media screen and (max-width: 768px) {
            .container {
                padding: 20px;
            }

            th, td {
                font-size: 13px;
                padding: 10px;
            }

            h2 {
                font-size: 22px;
            }
        }
    </style>
</head>


<body>
    <div class="container">
        <h2>Your Attendance Records</h2>

        <div class="info">
            <p><strong>Intern ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>
            <p><strong>Intern Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        </div>

        <?php if ($attendanceRecords && count($attendanceRecords) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Intern ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceRecords as $record): ?>
                        <?php
                            $statusClass = '';
                            if ($record['status'] === 'present') $statusClass = 'status-present';
                            elseif ($record['status'] === 'late') $statusClass = 'status-late';
                            elseif ($record['status'] === 'absent') $statusClass = 'status-absent';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($record['date']); ?></td>
                            <td><?php echo htmlspecialchars($record['check_in']); ?></td>
                            <td><?php echo htmlspecialchars($record['check_out']); ?></td>
                            <td class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($record['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no attendance records yet.</p>
        <?php endif; ?>

        <a href="jv-logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
