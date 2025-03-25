<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'intern') {
    header("Location: login.php");
    exit();
}

// Connect to database
$host = 'localhost';
$db = 'feedback_system';
$user = 'root';
$pass = '';  

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$intern_id = $_SESSION['user_id'];

// Fetch supervisor and customer feedback
$supervisor_query = "SELECT work_quality, communication, professionalism, created_at
                     FROM supervisor_feedback
                     WHERE intern_id = $intern_id
                     ORDER BY created_at ASC";
$supervisor_result = $conn->query($supervisor_query);

$customer_query = "SELECT professionalism, communication, service_quality, created_at
                   FROM customer_feedback
                   WHERE intern_id = $intern_id
                   ORDER BY created_at ASC";
$customer_result = $conn->query($customer_query);

$supervisor_data = [];
$customer_data = [];

while ($row = $supervisor_result->fetch_assoc()) {
    $supervisor_data[] = $row;
}

while ($row = $customer_result->fetch_assoc()) {
    $customer_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Intern Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<nav>
    <a href="intern_dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <h1>Intern Dashboard</h1>

    <div class="dashboard">
        <div class="chart-container">
            <h2>Supervisor Feedback</h2>
            <canvas id="supervisorChart"></canvas>
        </div>

        <div class="chart-container">
            <h2>Customer Feedback</h2>
            <canvas id="customerChart"></canvas>
        </div>
    </div>

    <h2>Feedback Details</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Work Quality</th>
                <th>Communication</th>
                <th>Professionalism</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($supervisor_data as $data): ?>
            <tr>
                <td><?= $data['created_at'] ?></td>
                <td><?= $data['work_quality'] ?></td>
                <td><?= $data['communication'] ?></td>
                <td><?= $data['professionalism'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <footer>Feedback System © 2025</footer>
</div>

<script>
    // Chart Data
    const supervisorLabels = <?= json_encode(array_column($supervisor_data, 'created_at')) ?>;
    const workQuality = <?= json_encode(array_column($supervisor_data, 'work_quality')) ?>;
    const communication = <?= json_encode(array_column($supervisor_data, 'communication')) ?>;
    const professionalism = <?= json_encode(array_column($supervisor_data, 'professionalism')) ?>;

    // Supervisor Chart
    new Chart(document.getElementById('supervisorChart'), {
        type: 'line',
        data: {
            labels: supervisorLabels,
            datasets: [
                { label: 'Work Quality', data: workQuality, borderColor: 'blue', fill: false },
                { label: 'Communication', data: communication, borderColor: 'green', fill: false },
                { label: 'Professionalism', data: professionalism, borderColor: 'orange', fill: false }
            ]
        },
        options: { responsive: true }
    });
</script>

</body>
</html>

<?php
$conn->close();
?>
