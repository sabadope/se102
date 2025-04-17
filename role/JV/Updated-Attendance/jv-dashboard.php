<?php
session_start();
include 'jv-db.php'; // Database connection

// Ensure user is logged in and is a supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header('Location: jv-login.php');
    exit;
}

// Set threshold for "Late" status (e.g., after 9:00 AM)
$lateThreshold = '09:00:00';

// Fetch all attendance records
$sql = "SELECT * FROM attendance ORDER BY date DESC";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute();
    $attendanceRecords = $stmt->fetchAll();
} catch (PDOException $e) {
    // Handle query execution error
    echo "Error fetching attendance records: " . $e->getMessage();
    exit;
}

// Check if there are no records retrieved
if (!$attendanceRecords) {
    echo "No attendance records found.";
    exit;
}

// Initialize counters for present, late, and absent
$attendanceCount = [
    'present' => 0,
    'late' => 0,
    'absent' => 0,
];

// Categorize attendance based on the check-in time and late threshold
foreach ($attendanceRecords as $record) {
    if ($record['status'] == 'present') {
        $attendanceCount['present']++;
    } elseif ($record['status'] == 'absent') {
        $attendanceCount['absent']++;
    }
    // Check if the record should be classified as "late"
    // Only consider check-in time for late classification
    if ($record['status'] != 'absent' && $record['check_in'] > $lateThreshold) {
        $attendanceCount['late']++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard - Attendance</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Reset and Base Styles */
* 
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    
    padding: 100px 30px 30px; /* ← Add enough top padding for the fixed navbar */
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 30px;
    font-size: 18px;
    background: linear-gradient(145deg, #e3f2fd, #f1f8e9);
}



/* Main Containers */
.main-container {
    background-color: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    padding: 30px;
    width: 60%;
    max-width: 900px;
}

.sidebar-container {
    background-color: #f8f9fa;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    padding: 30px;
    width: 35%;
    max-width: 400px;
}

/* Headers */
h2 {
    color: #333;
    text-align: center;
    margin-bottom: 25px;
    font-size: 26px;
}

/* Button */
.action-button {
    background-color: #2185d0;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 12px 20px;
    font-size: 16px;
    cursor: pointer;
    text-decoration: none;
    margin-bottom: 25px;
    display: inline-block;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
    border-radius: 8px;
    overflow: hidden;
    font-size: 18px;
}

th {
    background-color: #4a76a8;
    color: white;
    padding: 14px;
    text-align: center;
    font-size: 18px;
}

td {
    padding: 14px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

tr:last-child td {
    border-bottom: none;
}

tr:nth-child(even) {
    background-color: #f7f7f7;
}

/* Status Styling */
.present {
    color: #27ae60;
    font-weight: bold;
}

.late {
    color: #f39c12;
    font-weight: bold;
}

.absent {
    color: #e74c3c;
    font-weight: bold;
}

/* Chart Container */
.chart-container {
    margin-bottom: 30px;
}

.chart-title {
    text-align: center;
    font-size: 18px;
    margin-bottom: 10px;
    color: #333;
}

/* Attendance List */
.attendance-list {
    margin-top: 20px;
}

.attendance-list h3 {
    text-align: center;
    font-size: 18px;
    margin-bottom: 20px;
}

.attendance-list ul {
    list-style-type: none;
    font-size: 16px;
}

.attendance-list li {
    padding: 8px 0;
    display: flex;
    align-items: center;
}

.attendance-list li:before {
    content: "•";
    margin-right: 10px;
    font-weight: bold;
    font-size: 20px;
}

/* Responsive */
@media (max-width: 900px) {
    body {
        flex-direction: column;
        align-items: center;
    }

    .main-container,
    .sidebar-container {
        width: 100%;
        max-width: 100%;
    }
}


a.active {
    font-weight: bold;
    text-decoration: underline;
    color: #f1c40f !important; /* Optional: yellow highlight */
}


    </style>
</head>
<body>
<?php $activePage = 'jv-supervisor_dashboard'; ?>
<?php include 'jv-navbar.php'; ?>
    <!-- Main Container with Attendance Table -->
    <div class="main-container">
        <h2>Intern Attendance Dashboard</h2>
        
        
        <table>
            <thead>
                <tr>
                    <th>Intern ID</th>
                    <th>Intern Name</th>
                    <th>Date</th>
                    <th>Check-in Time</th>
                    <th>Check-out Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendanceRecords as $record): ?>
                    <?php
                    // Fetch the intern's name based on user_id
                    $sqlUser = "SELECT username FROM users WHERE id = ?";
                    $stmtUser = $pdo->prepare($sqlUser);
                    $stmtUser->execute([$record['user_id']]);
                    $user = $stmtUser->fetch();
                    
                    // Determine status class
                    $statusClass = '';
                    if ($record['status'] === 'present') {
                        $statusClass = 'present';
                    } elseif ($record['check_in'] > $lateThreshold && $record['status'] !== 'absent') {
                        $statusClass = 'late';
                        $record['status'] = 'late';
                    } elseif ($record['status'] === 'absent') {
                        $statusClass = 'absent';
                    }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username'] ?? 'Unknown'); ?></td>
                        <td><?php echo htmlspecialchars($record['date']); ?></td>
                        <td><?php echo htmlspecialchars($record['check_in']); ?></td>
                        <td><?php echo htmlspecialchars($record['check_out']); ?></td>
                        <td class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($record['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Sidebar Container with Chart and Stats -->
    <div class="sidebar-container">
        <div class="chart-container">
            <div class="chart-title">Attendance Overview</div>
            <canvas id="attendanceChart"></canvas>
        </div>
        
        <div class="attendance-list">
            <h3>Attendance List</h3>
            <ul>
                <li><strong>Present:</strong> <?php echo $attendanceCount['present']; ?> intern(s)</li>
                <li><strong>Late:</strong> <?php echo $attendanceCount['late']; ?> intern(s)</li>
                <li><strong>Absent:</strong> <?php echo $attendanceCount['absent']; ?> intern(s)</li>
            </ul>
        </div>
    </div>

    <script>
        // Set up the attendance chart
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Late', 'Absent'],
                datasets: [{
                    data: [<?php echo $attendanceCount['present']; ?>, <?php echo $attendanceCount['late']; ?>, <?php echo $attendanceCount['absent']; ?>],
                    backgroundColor: [
                        '#27ae60', // Green for Present
                        '#f39c12', // Yellow/Orange for Late
                        '#e74c3c'  // Red for Absent
                    ],
                    borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    title: {
                        display: false
                    }
                },
                cutout: '65%'
            }
        });
    </script>
</body>
</html>