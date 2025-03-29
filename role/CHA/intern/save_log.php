<?php
$conn = new mysqli("localhost", "root", "", "intern_logs");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$type = $_POST['type'];
$task_name = $_POST['task_name'] ?? null;
$task_desc = $_POST['task_desc'] ?? null;
$start_time = $_POST['start_time'] ?? null;
$end_time = $_POST['end_time'] ?? null;
$status = $_POST['status'] ?? null;
$weekly_goals = $_POST['weekly_goals'] ?? null;
$achievements = $_POST['achievements'] ?? null;
$challenges = $_POST['challenges'] ?? null;
$lessons = $_POST['lessons'] ?? null;

$sql = "INSERT INTO logs (type, task_name, task_desc, start_time, end_time, status, weekly_goals, achievements, challenges, lessons)
        VALUES ('$type', '$task_name', '$task_desc', '$start_time', '$end_time', '$status', '$weekly_goals', '$achievements', '$challenges', '$lessons')";

if ($conn->query($sql)) {
    header("Location: index.php");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();