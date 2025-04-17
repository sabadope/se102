<?php
require_once 'cha-auth_check.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: cha-unauthorized.php");
    exit();
}

$host = 'localhost';
$db = 'feedback_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="feedback_report_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, [
    'Feedback Type', 
    'Intern Name', 
    'Evaluator Name', 
    'Work Quality', 
    'Communication', 
    'Professionalism', 
    'Service Quality', 
    'Comments', 
    'Date'
]);

// Fetch supervisor feedback
$supervisor_query = "SELECT 
    'Supervisor' AS type,
    CONCAT(u.first_name, ' ', u.last_name) AS intern_name,
    CONCAT(s.first_name, ' ', s.last_name) AS evaluator_name,
    sf.work_quality,
    sf.communication,
    sf.professionalism,
    NULL AS service_quality,
    sf.comments,
    sf.created_at
FROM supervisor_feedback sf
JOIN interns i ON sf.intern_id = i.id
JOIN users u ON i.user_id = u.id
JOIN users s ON sf.supervisor_id = s.id
ORDER BY sf.created_at DESC";

$supervisor_result = $conn->query($supervisor_query);
while ($row = $supervisor_result->fetch_assoc()) {
    fputcsv($output, $row);
}

// Fetch customer feedback
$customer_query = "SELECT 
    'Customer' AS type,
    CONCAT(u.first_name, ' ', u.last_name) AS intern_name,
    CONCAT(c.first_name, ' ', c.last_name) AS evaluator_name,
    NULL AS work_quality,
    cf.communication,
    cf.professionalism,
    cf.service_quality,
    cf.comments,
    cf.created_at
FROM customer_feedback cf
JOIN interns i ON cf.intern_id = i.id
JOIN users u ON i.user_id = u.id
JOIN users c ON cf.customer_id = c.id
ORDER BY cf.created_at DESC";

$customer_result = $conn->query($customer_query);
while ($row = $customer_result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$conn->close();
?>