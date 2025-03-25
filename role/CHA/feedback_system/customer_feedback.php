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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $intern_id = $_POST['intern_id'];
    $customer_id = $_POST['customer_id'];
    $professionalism = $_POST['professionalism'];
    $communication = $_POST['communication'];
    $service_quality = $_POST['service_quality'];
    $comments = $_POST['comments'];

    $sql = "INSERT INTO customer_feedback 
            (intern_id, customer_id, professionalism, communication, service_quality, comments) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiis", $intern_id, $customer_id, $professionalism, $communication, $service_quality, $comments);

    if ($stmt->execute()) {
        echo "Customer feedback submitted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Feedback</title>
    <link rel="stylesheet" href="styles.css">  <!-- Add your CSS file -->
</head>
<body>
    <h2>Customer Feedback Form</h2>
    <form action="customer_feedback.php" method="POST">
        <label>Intern ID:</label>
        <input type="number" name="intern_id" required>

        <label>Customer ID:</label>
        <input type="number" name="customer_id" required>

        <label>Professionalism (1-5):</label>
        <input type="number" name="professionalism" min="1" max="5" required>

        <label>Communication (1-5):</label>
        <input type="number" name="communication" min="1" max="5" required>

        <label>Service Quality (1-5):</label>
        <input type="number" name="service_quality" min="1" max="5" required>

        <label>Comments:</label>
        <textarea name="comments" rows="5" cols="40"></textarea>

        <button type="submit">Submit Feedback</button>
    </form>
</body>
</html>
