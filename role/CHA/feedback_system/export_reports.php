<?php
// Connect to database
$host = 'localhost';
$db = 'feedback_system';
$user = 'root';
$pass = '';  // Replace with your DB password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="feedback_reports.csv"');

$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, ['Type', 'Intern', 'Evaluator', 'Quality', 'Communication', 'Professionalism/Service Quality', 'Comments', 'Date']);

// Fetch supervisor feedback
$supervisor_query = "SELECT u.first_name AS intern, s.first_name AS supervisor, 
                        sf.work_quality, sf.communication, sf.professionalism, sf.comments, sf.created_at
                     FROM supervisor_feedback sf
                     JOIN interns i ON sf.intern_id = i.id
                     JOIN users u ON i.user_id = u.id
                     JOIN users s ON sf.supervisor_id = s.id";

$supervisor_result = $conn->query($supervisor_query);

while ($row = $supervisor_result->fetch_assoc()) {
    fputcsv($output, ['Supervisor', $row['intern'], $row['supervisor'], $row['work_quality'], 
                      $row['communication'], $row['professionalism'], $row['comments'], $row['created_at']]);
}

// Fetch customer feedback
$customer_query = "SELECT u.first_name AS intern, c.first_name AS customer, 
                        cf.professionalism, cf.communication, cf.service_quality, cf.comments, cf.created_at
                   FROM customer_feedback cf
                   JOIN interns i ON cf.intern_id = i.id
                   JOIN users u ON i.user_id = u.id
                   JOIN users c ON cf.customer_id = c.id";

$customer_result = $conn->query($customer_query);

while ($row = $customer_result->fetch_assoc()) {
    fputcsv($output, ['Customer', $row['intern'], $row['customer'], $row['professionalism'], 
                      $row['communication'], $row['service_quality'], $row['comments'], $row['created_at']]);
}

fclose($output);
$conn->close();
?>
