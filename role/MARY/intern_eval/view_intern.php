<?php
// Include database connection
include 'db_connect.php';

// Check if ID is set
if (isset($_GET['id'])) {
    $intern_id = $_GET['id'];

    // Fetch intern data
    $query = "SELECT * FROM interns WHERE intern_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $intern_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $intern = $result->fetch_assoc();
    } else {
        echo "No intern found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

// Update Skills
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_skills'])) {
    $new_skills = $_POST['skills'];

    $updateQuery = "UPDATE interns SET skills = ? WHERE intern_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $new_skills, $intern_id);
    $stmt->execute();

    header("Location: view_intern.php?id=$intern_id");
    exit;
}

// Update Feedback
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_feedback'])) {
    $new_feedback = $_POST['feedback'];

    $updateQuery = "UPDATE interns SET feedback = ? WHERE intern_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $new_feedback, $intern_id);
    $stmt->execute();

    header("Location: view_intern.php?id=$intern_id");
    exit;
}

// Delete Feedback
if (isset($_POST['delete_feedback'])) {
    $deleteQuery = "UPDATE interns SET feedback = '' WHERE intern_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $intern_id);
    $stmt->execute();

    header("Location: view_intern.php?id=$intern_id");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Intern Report</title>
    <link rel="stylesheet" href="view_intern.css">
</head>
<body>

<div class="containers">
    <div class="header-container">
        <h2>Intern's Full Report</h2>
    </div>
</div>

<!-- Back Button -->
<div class="back-container">
    <a href="view_evaluations.php" class="back-btn">← Back</a>
</div>
   
<!-- Intern Profile -->
<div class="intern-container">
<div class="intern-profile">
    <div class="rank-box">
        <p class="rank-text">Rank</p>
        <p class="rank-number"><?php echo $intern['ranking']; ?></p>
    </div>
    <div class="intern-info">
        <h4><?php echo $intern['name']; ?></h4>
        <p class="intern-id">ID: <?php echo $intern['intern_id']; ?></p>
    </div>
</div>

<!-- Clickable Containers (Attendance & Task Completion) -->
<div class="info-container">
    <a href="get_attendance.php?id=<?php echo $intern['intern_id']; ?>" class="info-box">
        <h3>Attendance</h3>
        <p><?php echo $intern['attendance']; ?>%</p>
    </a>


    <a href="#" class="info-box">
        <h3>Task Completion</h3>
        <p><?php echo $intern['tasks_completed']; ?></p>
    </a>
</div>


<!-- Skills Section -->
<div class="skills-container">
    <h3>Skills</h3>
    <form method="POST">
        <textarea name="skills" rows="3" cols="50"><?php echo $intern['skills']; ?></textarea><br>
        <button type="submit" name="update_skills" class="update-btn">Update Skills</button>
    </form>
</div>

<!-- Feedback Section -->
<div class="feedback-container">
    <h3>Feedback</h3>
    <form method="POST">
        <textarea name="feedback" rows="4" cols="50"><?php echo $intern['feedback']; ?></textarea><br>
        <button type="submit" name="update_feedback" class="update-btn">Update Feedback</button>
        <button type="submit" name="delete_feedback" class="delete-btn">Delete Feedback</button>
        </form>
    </div>
</div>

<script>
function showAttendance(intern_id) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("attendance-details").innerHTML = xhr.responseText;
            document.getElementById("attendance-modal").style.display = "block";
        }
    };
    xhr.open("GET", "get_attendance.php?intern_id=" + intern_id, true);
    xhr.send();
}

function closeModal() {
    document.getElementById("attendance-modal").style.display = "none";
}
</script>


</body>
</html>
