<?php
require_once 'cha-auth_check.php';
if ($_SESSION['role'] !== 'supervisor') {
    header("Location: cha-unauthorized.php");
    exit();
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$supervisor_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $intern_id = (int)$_POST['intern_id'];
    $work_quality = (int)$_POST['work_quality'];
    $communication = (int)$_POST['communication'];
    $professionalism = (int)$_POST['professionalism'];
    $comments = $conn->real_escape_string($_POST['comments']);

    $sql = "INSERT INTO supervisor_feedback 
            (intern_id, supervisor_id, work_quality, communication, professionalism, comments) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiis", $intern_id, $supervisor_id, $work_quality, $communication, $professionalism, $comments);

    if ($stmt->execute()) {
        $success = "Feedback submitted successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch interns for dropdown
$interns_query = "SELECT i.id, u.first_name, u.last_name 
                 FROM interns i
                 JOIN users u ON i.user_id = u.id
                 ORDER BY u.first_name";
$interns_result = $conn->query($interns_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Feedback</title>
    <link rel="stylesheet" href="cha-styles.css">
</head>
<body>
    <?php include 'cha-navbar.php'; ?>
    
    <div class="container">
        <h1>Supervisor Feedback Form</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert success"><?= $success ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form action="cha-supervisor_feedback.php" method="POST" class="feedback-form">
            <div class="form-group">
                <label for="intern_id">Select Intern:</label>
                <select name="intern_id" id="intern_id" required>
                    <option value="">-- Select Intern --</option>
                    <?php while ($intern = $interns_result->fetch_assoc()): ?>
                        <option value="<?= $intern['id'] ?>">
                            <?= htmlspecialchars($intern['first_name'] . ' ' . htmlspecialchars($intern['last_name'])) ?> <!-- Corrected line -->
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="rating-group">
                <label>Work Quality:</label>
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="wq-<?= $i ?>" name="work_quality" value="<?= $i ?>" required>
                        <label for="wq-<?= $i ?>">★</label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="rating-group">
                <label>Communication:</label>
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="com-<?= $i ?>" name="communication" value="<?= $i ?>" required>
                        <label for="com-<?= $i ?>">★</label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="rating-group">
                <label>Professionalism:</label>
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="pro-<?= $i ?>" name="professionalism" value="<?= $i ?>" required>
                        <label for="pro-<?= $i ?>">★</label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="comments">Comments:</label>
                <textarea name="comments" id="comments" rows="4" placeholder="Provide constructive feedback..."></textarea>
            </div>

            <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
    </div>

    
    <?php $conn->close(); ?>
</body>
</html>