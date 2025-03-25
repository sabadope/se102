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
    $supervisor_id = $_POST['supervisor_id'];
    $work_quality = $_POST['work_quality'];
    $communication = $_POST['communication'];
    $professionalism = $_POST['professionalism'];
    $comments = $_POST['comments'];

    $sql = "INSERT INTO supervisor_feedback 
            (intern_id, supervisor_id, work_quality, communication, professionalism, comments) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiis", $intern_id, $supervisor_id, $work_quality, $communication, $professionalism, $comments);

    if ($stmt->execute()) {
        echo "Feedback submitted successfully.";
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
    <title>Supervisor Feedback</title>
    <link rel="stylesheet" href="styles.css">  <!-- Add your CSS file -->
</head>
<body>
    <h2>Supervisor Feedback Form</h2>
    <form action="supervisor_feedback.php" method="POST">
        <label>Intern ID:</label>
        <input type="number" name="intern_id" required>

        <label>Supervisor ID:</label>
        <input type="number" name="supervisor_id" required>

        <label>Work Quality (1-5):</label>
        <input type="number" name="work_quality" min="1" max="5" required>

        <label>Communication (1-5):</label>
        <input type="number" name="communication" min="1" max="5" required>

        <label>Professionalism (1-5):</label>
        <input type="number" name="professionalism" min="1" max="5" required>

        <label>Comments:</label>
        <textarea name="comments" rows="5" cols="40"></textarea>

        <button type="submit">Submit Feedback</button>
    </form>
</body>
</html>
