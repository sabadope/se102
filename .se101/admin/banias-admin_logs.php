<?php
session_start();
require_once 'banias-db_connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: banias-login.php');
    exit();
}

// Handle log deletion
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $delete_query = "DELETE FROM logs WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: banias-admin_logs.php');
    exit();
}

// Handle supervisor review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_submit'])) {
    $log_id = (int)$_POST['log_id'];
    $feedback = $conn->real_escape_string($_POST['feedback']);
    $rating = (int)$_POST['rating'];
    
    $review_query = "UPDATE logs SET supervisor_feedback = ?, supervisor_rating = ? WHERE id = ?";
    $review_stmt = $conn->prepare($review_query);
    $review_stmt->bind_param("sii", $feedback, $rating, $log_id);
    $review_stmt->execute();
    header('Location: banias-admin_logs.php');
    exit();
}

// Get all logs with user information
$query = "SELECT l.*, u.username, u.full_name 
          FROM logs l 
          JOIN users u ON l.user_id = u.id 
          ORDER BY l.timestamp DESC";
$logs = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs Management - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #94a3b8;
            --border-radius: 8px;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: #f1f5f9;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.875rem;
            color: var(--dark);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .logs-table {
            width: 100%;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .logs-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .logs-table th,
        .logs-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .logs-table th {
            background: var(--light);
            font-weight: 600;
            color: var(--dark);
        }

        .logs-table tr:hover {
            background: var(--light);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-completed {
            background: #dcfce7;
            color: #166534;
        }

        .status-in-progress {
            background: #dbeafe;
            color: #1e40af;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            max-width: 500px;
            width: 100%;
        }

        .modal-header {
            margin-bottom: 1.5rem;
        }

        .modal-header h2 {
            color: var(--dark);
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-weight: 500;
        }

        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--border-radius);
            font-size: 1rem;
            resize: vertical;
            min-height: 100px;
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

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Logs Management</h1>
            <a href="banias-admin.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>

        <div class="logs-table">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Task</th>
                        <th>Time Spent</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = $logs->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div><?= htmlspecialchars($log['username']) ?></div>
                                <small class="text-gray"><?= htmlspecialchars($log['full_name']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($log['task_name']) ?></td>
                            <td><?= htmlspecialchars($log['start_time']) ?> - <?= htmlspecialchars($log['end_time']) ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower($log['status']) ?>">
                                    <?= ucfirst($log['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($log['timestamp'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button onclick="openReviewModal(<?= htmlspecialchars(json_encode($log)) ?>)" class="btn btn-primary">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <button onclick="confirmDelete(<?= $log['id'] ?>)" class="btn btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Review Log</h2>
            </div>
            <form action="" method="POST">
                <input type="hidden" name="log_id" id="review_log_id">
                <div class="form-group">
                    <label>Rating:</label>
                    <div class="rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" name="rating" id="rating<?= $i ?>" value="<?= $i ?>">
                            <label for="rating<?= $i ?>"><i class="fas fa-star"></i></label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="feedback">Feedback:</label>
                    <textarea id="feedback" name="feedback" placeholder="Enter your feedback..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeReviewModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="review_submit" class="btn btn-primary">Save Review</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReviewModal(log) {
            document.getElementById('reviewModal').style.display = 'flex';
            document.getElementById('review_log_id').value = log.id;
            document.getElementById('feedback').value = log.supervisor_feedback || '';
            
            // Set the rating if it exists
            if (log.supervisor_rating) {
                document.querySelector(`input[name="rating"][value="${log.supervisor_rating}"]`).checked = true;
            }
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
        }

        function confirmDelete(logId) {
            if (confirm('Are you sure you want to delete this log?')) {
                window.location.href = `?delete=1&id=${logId}`;
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('reviewModal');
            if (event.target === modal) {
                closeReviewModal();
            }
        }

        // Initialize star ratings
        document.addEventListener('DOMContentLoaded', function() {
            const ratingInputs = document.querySelectorAll('.rating input[type="radio"]');
            const ratingLabels = document.querySelectorAll('.rating label');

            ratingLabels.forEach((label, index) => {
                label.addEventListener('mouseover', () => {
                    for (let i = 0; i <= index; i++) {
                        ratingLabels[i].style.color = '#f1c40f';
                    }
                    for (let i = index + 1; i < ratingLabels.length; i++) {
                        ratingLabels[i].style.color = '#e0e0e0';
                    }
                });
            });

            document.querySelector('.rating').addEventListener('mouseleave', () => {
                const checkedInput = document.querySelector('.rating input[type="radio"]:checked');
                if (checkedInput) {
                    const index = Array.from(ratingInputs).indexOf(checkedInput);
                    for (let i = 0; i <= index; i++) {
                        ratingLabels[i].style.color = '#f1c40f';
                    }
                    for (let i = index + 1; i < ratingLabels.length; i++) {
                        ratingLabels[i].style.color = '#e0e0e0';
                    }
                } else {
                    ratingLabels.forEach(label => {
                        label.style.color = '#e0e0e0';
                    });
                }
            });
        });
    </script>
</body>
</html> 