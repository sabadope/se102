<?php
include 'db_connect.php';

// Check if an intern ID is provided
if (isset($_GET['id'])) {
    $intern_id = $_GET['id'];

    // Fetch attendance records
    $query = "SELECT date, time_in, time_out, marked FROM attendance WHERE intern_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $intern_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "Invalid Intern ID";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Record</title>
    <link rel="stylesheet" href="view_intern.css">
</head>
<body>

<!-- Back Button -->
<div class="back-container">
    <a href="javascript:history.back()" class="back-btn">← Back</a>
</div>

<!-- Attendance Section -->
<div class="attendance-container">
    <h3>Attendance Record</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Marked</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['date']}</td>
                            <td>{$row['time_in']}</td>
                            <td>{$row['time_out']}</td>
                            <td>{$row['marked']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No attendance records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
