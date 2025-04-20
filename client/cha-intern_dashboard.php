<?php
require_once 'cha-auth_check.php';
if ($_SESSION['role'] !== 'intern') {
    header("Location: cha-unauthorized.php");
    exit();
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$intern_id = $_SESSION['user_id'];

// Get intern info
$intern_query = "SELECT i.id, u.first_name, u.last_name, i.department 
                FROM interns i 
                JOIN users u ON i.user_id = u.id 
                WHERE u.id = ?";
$stmt = $conn->prepare($intern_query);
$stmt->bind_param("i", $intern_id);
$stmt->execute();
$intern_result = $stmt->get_result();
$intern = $intern_result->fetch_assoc();
$stmt->close();

// Get supervisor feedback
$supervisor_query = "SELECT sf.work_quality, sf.communication, sf.professionalism, 
                    sf.comments, sf.created_at, u.first_name AS supervisor_name
                 FROM supervisor_feedback sf
                 JOIN users u ON sf.supervisor_id = u.id
                 WHERE sf.intern_id = ?
                 ORDER BY sf.created_at DESC";
$stmt = $conn->prepare($supervisor_query);
$stmt->bind_param("i", $intern['id']);
$stmt->execute();
$supervisor_result = $stmt->get_result();
$supervisor_feedback = $supervisor_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get customer feedback
$customer_query = "SELECT cf.professionalism, cf.communication, cf.service_quality, 
                  cf.comments, cf.created_at
               FROM customer_feedback cf
               WHERE cf.intern_id = ?
               ORDER BY cf.created_at DESC";
$stmt = $conn->prepare($customer_query);
$stmt->bind_param("i", $intern['id']);
$stmt->execute();
$customer_result = $stmt->get_result();
$customer_feedback = $customer_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Dashboard</title>
    <link rel="stylesheet" href="cha-styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'cha-navbar.php'; ?>
    
    <div class="container">
        <div class="profile-header">
            <h1>Welcome, <?= htmlspecialchars($intern['first_name'] . ' ' . htmlspecialchars($intern['last_name'])) ?>!</h1> <!-- Corrected line -->
            <p class="department">Department: <?= htmlspecialchars($intern['department']) ?></p>
        </div>

        <hr class="divider">

        <div class="dashboard-grid">
            <div class="card">
                <h2>Performance Overview</h2>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <div class="card">
                <h2>Recent Supervisor Feedback</h2>
                <?php if (!empty($supervisor_feedback)): ?>
                    <div class="feedback-item">
                        <div class="rating-row">
                            <span class="rating-label">Work Quality:</span>
                            <span class="rating-stars"><?= str_repeat('★', $supervisor_feedback[0]['work_quality']) . str_repeat('☆', 5 - $supervisor_feedback[0]['work_quality']) ?></span>
                        </div>
                        <div class="rating-row">
                            <span class="rating-label">Communication:</span>
                            <span class="rating-stars"><?= str_repeat('★', $supervisor_feedback[0]['communication']) . str_repeat('☆', 5 - $supervisor_feedback[0]['communication']) ?></span>
                        </div>
                        <div class="rating-row">
                            <span class="rating-label">Professionalism:</span>
                            <span class="rating-stars"><?= str_repeat('★', $supervisor_feedback[0]['professionalism']) . str_repeat('☆', 5 - $supervisor_feedback[0]['professionalism']) ?></span>
                        </div>
                        <p class="comments"><?= htmlspecialchars($supervisor_feedback[0]['comments']) ?></p>
                        <p class="meta">From: <?= htmlspecialchars($supervisor_feedback[0]['supervisor_name']) ?> on <?= date('M d, Y', strtotime($supervisor_feedback[0]['created_at'])) ?></p>
                    </div>
                <?php else: ?>
                    <p class="no-feedback">No supervisor feedback yet.</p>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>Recent Customer Feedback</h2>
                <?php if (!empty($customer_feedback)): ?>
                    <div class="feedback-item">
                        <div class="rating-row">
                            <span class="rating-label">Professionalism:</span>
                            <span class="rating-stars"><?= str_repeat('★', $customer_feedback[0]['professionalism']) . str_repeat('☆', 5 - $customer_feedback[0]['professionalism']) ?></span>
                        </div>
                        <div class="rating-row">
                            <span class="rating-label">Communication:</span>
                            <span class="rating-stars"><?= str_repeat('★', $customer_feedback[0]['communication']) . str_repeat('☆', 5 - $customer_feedback[0]['communication']) ?></span>
                        </div>
                        <div class="rating-row">
                            <span class="rating-label">Service Quality:</span>
                            <span class="rating-stars"><?= str_repeat('★', $customer_feedback[0]['service_quality']) . str_repeat('☆', 5 - $customer_feedback[0]['service_quality']) ?></span>
                        </div>
                        <p class="comments"><?= htmlspecialchars($customer_feedback[0]['comments']) ?></p>
                        <p class="meta">Received on <?= date('M d, Y', strtotime($customer_feedback[0]['created_at'])) ?></p>
                    </div>
                <?php else: ?>
                    <p class="no-feedback">No customer feedback yet.</p>
                <?php endif; ?>
            </div>

            <div class="card full-width">
                <h2>All Feedback History</h2>
                <div class="tabs">
                    <button class="tab-btn active" onclick="openTab('supervisor-tab')">Supervisor Feedback</button>
                    <button class="tab-btn" onclick="openTab('customer-tab')">Customer Feedback</button>
                </div>

                <div id="supervisor-tab" class="tab-content" style="display: block;">
                    <?php if (!empty($supervisor_feedback)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Supervisor</th>
                                    <th>Work Quality</th>
                                    <th>Communication</th>
                                    <th>Professionalism</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($supervisor_feedback as $feedback): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($feedback['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($feedback['supervisor_name']) ?></td>
                                    <td><?= str_repeat('★', $feedback['work_quality']) . str_repeat('☆', 5 - $feedback['work_quality']) ?></td>
                                    <td><?= str_repeat('★', $feedback['communication']) . str_repeat('☆', 5 - $feedback['communication']) ?></td>
                                    <td><?= str_repeat('★', $feedback['professionalism']) . str_repeat('☆', 5 - $feedback['professionalism']) ?></td>
                                    <td><?= htmlspecialchars($feedback['comments']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="no-feedback">No supervisor feedback history available.</p>
                    <?php endif; ?>
                </div>

                <div id="customer-tab" class="tab-content">
                    <?php if (!empty($customer_feedback)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Professionalism</th>
                                    <th>Communication</th>
                                    <th>Service Quality</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customer_feedback as $feedback): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($feedback['created_at'])) ?></td>
                                    <td><?= str_repeat('★', $feedback['professionalism']) . str_repeat('☆', 5 - $feedback['professionalism']) ?></td>
                                    <td><?= str_repeat('★', $feedback['communication']) . str_repeat('☆', 5 - $feedback['communication']) ?></td>
                                    <td><?= str_repeat('★', $feedback['service_quality']) . str_repeat('☆', 5 - $feedback['service_quality']) ?></td>
                                    <td><?= htmlspecialchars($feedback['comments']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="no-feedback">No customer feedback history available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Performance Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Work Quality', 'Communication', 'Professionalism', 'Service Quality'],
                datasets: [
                    {
                        label: 'Supervisor Ratings',
                        data: [
                            <?= !empty($supervisor_feedback) ? round(array_sum(array_column($supervisor_feedback, 'work_quality')) / count($supervisor_feedback), 2) : 0 ?>,
                            <?= !empty($supervisor_feedback) ? round(array_sum(array_column($supervisor_feedback, 'communication')) / count($supervisor_feedback), 2) : 0 ?>,
                            <?= !empty($supervisor_feedback) ? round(array_sum(array_column($supervisor_feedback, 'professionalism')) / count($supervisor_feedback), 2) : 0 ?>,
                            0
                        ],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Customer Ratings',
                        data: [
                            0,
                            <?= !empty($customer_feedback) ? round(array_sum(array_column($customer_feedback, 'communication')) / count($customer_feedback), 2) : 0 ?>,
                            <?= !empty($customer_feedback) ? round(array_sum(array_column($customer_feedback, 'professionalism')) / count($customer_feedback), 2) : 0 ?>,
                            <?= !empty($customer_feedback) ? round(array_sum(array_column($customer_feedback, 'service_quality')) / count($customer_feedback), 2) : 0 ?>
                        ],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                scales: {
                    r: {
                        angleLines: { display: true },
                        suggestedMin: 0,
                        suggestedMax: 5
                    }
                }
            }
        });

        // Tab functionality
        function openTab(tabId) {
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].style.display = 'none';
            }

            const tabButtons = document.getElementsByClassName('tab-btn');
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }

            document.getElementById(tabId).style.display = 'block';
            event.currentTarget.classList.add('active');
        }
    </script>

    
    <?php $conn->close(); ?>
</body>
</html>