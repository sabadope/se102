<?php
session_start();
include 'banias-db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: banias-login.php");
    exit();
}

// Initialize variables
$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));
$start_date = date('M j', strtotime($monday));
$end_date = date('M j, Y', strtotime($sunday));
$user_id = (int)$_SESSION['user_id'];

// Handle delete action
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Add user_id check to prevent unauthorized deletion
    $delete_query = "DELETE FROM logs WHERE id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ii", $id, $user_id);
    if ($delete_stmt->execute()) {
        header("Location: banias-weekly_summary.php");
        exit();
    }
}

// Handle supervisor review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_submit'])) {
    $log_id = (int)$_POST['log_id'];
    $feedback = $conn->real_escape_string($_POST['feedback']);
    $rating = (int)$_POST['rating'];
    
    // Add user_id check to prevent unauthorized updates
    $review_query = "UPDATE logs SET supervisor_feedback = ?, supervisor_rating = ? WHERE id = ? AND user_id = ?";
    $review_stmt = $conn->prepare($review_query);
    $review_stmt->bind_param("siii", $feedback, $rating, $log_id, $user_id);
    $review_stmt->execute();
}

// Query to get weekly logs for the current user
$query = "SELECT 
            id,
            DATE(timestamp) as date,
            task_name as task,
            CONCAT(TIME_FORMAT(start_time, '%h:%i%p'), ' - ', TIME_FORMAT(end_time, '%h:%i%p')) as time_spent,
            challenges,
            lessons as improvements,
            status,
            supervisor_feedback,
            supervisor_rating
          FROM logs 
          WHERE user_id = ?
          ORDER BY timestamp DESC";

$stmt = $conn->prepare($query);
if ($stmt === false) die('Prepare failed: ' . htmlspecialchars($conn->error));

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) die('Execute failed: ' . htmlspecialchars($stmt->error));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Performance Summary</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2c3e50;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #7f8c8d;
            --light-gray: #e0e0e0;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.05);
            --transition: all 0.2s ease;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f9f9f9;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            margin-bottom: 2rem;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            transition: var(--transition);
        }
        
        .back-btn:hover {
            color: var(--primary-dark);
            transform: translateX(-3px);
        }
        
        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--light-gray);
            padding-bottom: 1rem;
        }
        
        .tab {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            border-radius: var(--border-radius);
            transition: var(--transition);
            color: var(--gray);
            font-weight: 500;
        }
        
        .tab:hover {
            background: var(--light);
            color: var(--dark);
        }
        
        .tab.active {
            background: var(--primary);
            color: white;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .table-container {
            overflow-x: auto;
            margin-bottom: 1.5rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }
        
        th {
            background: var(--light);
            font-weight: 600;
            color: var(--dark);
        }
        
        tr:hover {
            background: var(--light);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            text-decoration: none;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-outline {
            background: white;
            border: 1px solid var(--light-gray);
            color: var(--gray);
        }
        
        .btn-outline:hover {
            background: var(--light);
            color: var(--dark);
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .review-form {
            background: var(--light);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
        }
        
        .review-form textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            resize: vertical;
        }
        
        .rating {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .rating input[type="radio"] {
            display: none;
        }
        
        .rating label {
            cursor: pointer;
            font-size: 1.5rem;
            color: var(--light-gray);
        }
        
        .rating input[type="radio"]:checked + label {
            color: #f1c40f;
        }
        
        /* Added hover effect for the rating stars */
        .rating label:hover,
        .rating label:hover ~ label {
            color: #f1c40f !important;
        }
        
        .supervisor-feedback {
            background: var(--light);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-top: 0.5rem;
        }
        
        .supervisor-feedback p {
            margin-bottom: 0.5rem;
        }
        
        .rating-display {
            color: #f1c40f;
            font-size: 1.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="banias-index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Logs
            </a>
            <h1>Weekly Performance Summary</h1>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Week of <?= $start_date ?> - <?= $end_date ?></div>
            </div>
            
            <div class="card-body">
                <div class="tabs">
                    <div class="tab active" onclick="openTab('intern')">
                        <i class="fas fa-user-graduate"></i> Intern View
                    </div>
                    <div class="tab" onclick="openTab('supervisor')">
                        <i class="fas fa-clipboard-check"></i> Supervisor Review
                    </div>
                </div>
                
                <!-- Intern View -->
                <div id="intern" class="tab-content active">
                    <h2>Weekly Activities</h2>
                    
                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Task</th>
                                        <th>Time Spent</th>
                                        <th>Status</th>
                                        <th>Challenges</th>
                                        <th>Improvements</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= date('M j, Y', strtotime($row['date'])) ?></td>
                                            <td><?= htmlspecialchars($row['task']) ?></td>
                                            <td><?= htmlspecialchars($row['time_spent']) ?></td>
                                            <td>
                                                <span class="status-badge <?= strtolower($row['status']) ?>">
                                                    <?= htmlspecialchars($row['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($row['challenges']) ?></td>
                                            <td><?= htmlspecialchars($row['improvements']) ?></td>
                                            <td>
                                                <button onclick="if(confirm('Are you sure you want to delete this log?')) window.location.href='?delete=1&id=<?= $row['id'] ?>'" 
                                                        class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="action-buttons">
                            <button onclick="window.print()" class="btn btn-outline">
                                <i class="fas fa-print"></i> Print Summary
                            </button>
                            <button onclick="window.location.href='banias-export_weekly.php?start_date=<?= $monday ?>&end_date=<?= $sunday ?>'" 
                                class="btn btn-primary">
                                <i class="fas fa-file-export"></i> Export Weekly Data
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <h3>No logs found for this week</h3>
                            <p>Start by adding daily logs to see them appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Supervisor Review -->
                <div id="supervisor" class="tab-content">
                    <h2>Supervisor Review</h2>
                    
                    <?php 
                    // Reset the result pointer
                    $result->data_seek(0);
                    
                    if ($result && $result->num_rows > 0): 
                        while ($row = $result->fetch_assoc()): 
                    ?>
                        <div class="card" style="margin-bottom: 1rem;">
                            <div class="card-body">
                                <h3><?= htmlspecialchars($row['task']) ?></h3>
                                <p><strong>Date:</strong> <?= date('M j, Y', strtotime($row['date'])) ?></p>
                                <p><strong>Time Spent:</strong> <?= htmlspecialchars($row['time_spent']) ?></p>
                                <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
                                
                                <?php if ($row['supervisor_feedback']): ?>
                                    <div class="supervisor-feedback">
                                        <h4>Previous Feedback</h4>
                                        <p><?= htmlspecialchars($row['supervisor_feedback']) ?></p>
                                        <div class="rating-display">
                                            <?php for ($i = 0; $i < $row['supervisor_rating']; $i++): ?>
                                                <i class="fas fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <form action="" method="POST" class="review-form">
                                    <input type="hidden" name="log_id" value="<?= $row['id'] ?>">
                                    <div class="form-group">
                                        <label>Rating:</label>
                                        <div class="rating" id="rating-container-<?= $row['id'] ?>">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <input type="radio" name="rating" id="rating<?= $row['id'] ?>_<?= $i ?>" value="<?= $i ?>" 
                                                       <?= ($row['supervisor_rating'] == $i) ? 'checked' : '' ?>>
                                                <label for="rating<?= $row['id'] ?>_<?= $i ?>"><i class="fas fa-star"></i></label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Feedback:</label>
                                        <textarea name="feedback" rows="3" placeholder="Enter your feedback..."><?= htmlspecialchars($row['supervisor_feedback'] ?? '') ?></textarea>
                                    </div>
                                    <button type="submit" name="review_submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Review
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                        <div class="empty-state">
                            <i class="fas fa-clipboard-check"></i>
                            <h3>No logs to review</h3>
                            <p>There are no logs available for review this week</p>
                        </div>
                        <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function openTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show the selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to the clicked tab
            event.currentTarget.classList.add('active');
            
            // Initialize star ratings when supervisor tab is opened
            if (tabName === 'supervisor') {
                initStarRating();
            }
        }
        
        function initStarRating() {
            // Get all star rating containers
            const ratingContainers = document.querySelectorAll('.rating');
            
            ratingContainers.forEach(container => {
                const stars = container.querySelectorAll('label');
                const inputs = container.querySelectorAll('input');
                
                // Add hover effect
                stars.forEach((star, index) => {
                    star.addEventListener('mouseover', () => {
                        // Highlight current star and all previous stars
                        for (let i = 0; i <= index; i++) {
                            stars[i].style.color = '#f1c40f';
                        }
                        // Remove highlight from following stars
                        for (let i = index + 1; i < stars.length; i++) {
                            stars[i].style.color = '#e0e0e0';
                        }
                    });
                    
                    // Add click handler
                    star.addEventListener('click', () => {
                        inputs[index].checked = true;
                        
                        // Update star colors
                        for (let i = 0; i <= index; i++) {
                            stars[i].style.color = '#f1c40f';
                        }
                        for (let i = index + 1; i < stars.length; i++) {
                            stars[i].style.color = '#e0e0e0';
                        }
                    });
                });
                
                // Reset stars on mouse leave if no rating is selected
                container.addEventListener('mouseleave', () => {
                    const checkedInput = Array.from(inputs).findIndex(input => input.checked);
                    
                    if (checkedInput >= 0) {
                        // A rating is selected, highlight up to that star
                        for (let i = 0; i <= checkedInput; i++) {
                            stars[i].style.color = '#f1c40f';
                        }
                        for (let i = checkedInput + 1; i < stars.length; i++) {
                            stars[i].style.color = '#e0e0e0';
                        }
                    } else {
                        // No rating selected, reset all stars
                        stars.forEach(star => {
                            star.style.color = '#e0e0e0';
                        });
                    }
                });
                
                // Initialize the star colors based on current value
                const checkedInput = Array.from(inputs).findIndex(input => input.checked);
                if (checkedInput >= 0) {
                    for (let i = 0; i <= checkedInput; i++) {
                        stars[i].style.color = '#f1c40f';
                    }
                }
            });
        }
        
        // Call the function when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize star ratings if supervisor tab is active on page load
            if (document.getElementById('supervisor').classList.contains('active')) {
                initStarRating();
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>