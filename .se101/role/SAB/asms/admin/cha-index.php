<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "intern_logs");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch saved logs
$sql = "SELECT * FROM logs ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Performance Logs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --danger: #f72585;
            --success: #4cc9f0;
            --border-radius: 8px;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
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
            background-color: #f5f7fa;
            padding: 0;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        h1 {
            color: var(--primary);
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .subtitle {
            color: var(--gray);
            font-size: 1rem;
            margin-bottom: 2rem;
        }
        
        .tabs {
            display: flex;
            border-bottom: 1px solid var(--light-gray);
            margin-bottom: 2rem;
        }
        
        .tab {
            padding: 1rem 2rem;
            cursor: pointer;
            font-weight: 500;
            color: var(--gray);
            border-bottom: 3px solid transparent;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        
        .tab:hover:not(.active) {
            color: var(--dark);
        }
        
        .tab i {
            font-size: 1rem;
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        input, select, textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .time-fields {
            display: flex;
            gap: 1rem;
        }
        
        .time-fields > div {
            flex: 1;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: white;
            border: 1px solid var(--light-gray);
            color: var(--dark);
        }
        
        .btn-outline:hover {
            background: var(--light);
            border-color: var(--gray);
        }
        
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .saved-logs {
            margin-top: 3rem;
        }
        
        .saved-logs h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        .log-entry {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        
        .log-entry:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .log-entry h3 {
            margin-top: 0;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .log-entry p {
            margin-bottom: 0.5rem;
        }
        
        .log-entry strong {
            color: var(--dark);
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--light-gray);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .tabs {
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }
            
            .tab {
                white-space: nowrap;
                padding: 0.75rem 1rem;
            }
            
            .time-fields {
                flex-direction: column;
                gap: 1rem;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Intern Performance Logs</h1>
            <p class="subtitle">Track and manage your daily and weekly activities</p>
        </div>
        
        <div class="card">
            <div class="tabs">
                <div class="tab active" onclick="openTab('daily')">
                    <i class="fas fa-calendar-day"></i> Daily Log
                </div>
                <div class="tab" onclick="openTab('weekly')">
                    <i class="fas fa-calendar-week"></i> Weekly Log
                </div>
            </div>
            
            <!-- Daily Log Content -->
            <div id="daily" class="tab-content active">
                <form action="save_log.php" method="POST">
                    <input type="hidden" name="type" value="Daily Log">
                    
                    <div class="form-group">
                        <label>Task Name:</label>
                        <input type="text" name="task_name" placeholder="Enter task name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Task Description:</label>
                        <textarea name="task_desc" placeholder="Describe the task in detail" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Time:</label>
                        <div class="time-fields">
                            <div>
                                <label>Start Time</label>
                                <input type="time" name="start_time" required>
                            </div>
                            <div>
                                <label>End Time</label>
                                <input type="time" name="end_time" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Status:</label>
                        <select name="status" required>
                            <option value="">Select status</option>
                            <option value="Completed">Completed</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Daily Log
                        </button>
                        <button type="button" class="btn btn-outline" onclick="window.location.href='weekly_summary.php'">
                            <i class="fas fa-chart-bar"></i> View Weekly Summary
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Weekly Log Content -->
            <div id="weekly" class="tab-content">
                <form action="save_log.php" method="POST">
                    <input type="hidden" name="type" value="Weekly Log">
                    
                    <div class="form-group">
                        <h3>Weekly Goals:</h3>
                        <textarea name="weekly_goals" placeholder="What were your goals for this week?" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Achievements:</label>
                        <textarea name="achievements" placeholder="What did you accomplish this week?" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Challenges:</label>
                        <textarea name="challenges" placeholder="What challenges did you encounter?" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Lessons Learned:</label>
                        <textarea name="lessons" placeholder="What did you learn this week?" required></textarea>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Weekly Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Saved Logs Section -->
        <div class="saved-logs">
            <h2><i class="fas fa-history"></i> Recent Logs</h2>
            
            <?php if ($recent_logs->num_rows> 0): ?>
                <?php while ($row = $recent_logs->fetch_assoc()): ?>
                    <div class="log-entry">
                        <h3>
                            <?php if ($row['type'] === 'Weekly Log'): ?>
                                <i class="fas fa-calendar-week"></i>
                            <?php else: ?>
                                <i class="fas fa-calendar-day"></i>
                            <?php endif; ?>
                            <?= $row['type'] ?> - <?= date('M j, Y', strtotime($row['timestamp'])) ?>
                        </h3>
                        
                        <?php if ($row['type'] === 'Weekly Log'): ?>
                            <div class="weekly-log-details">
                                <p><strong>Goals:</strong> <?= htmlspecialchars($row['weekly_goals']) ?></p>
                                <p><strong>Achievements:</strong> <?= htmlspecialchars($row['achievements']) ?></p>
                                <p><strong>Challenges:</strong> <?= htmlspecialchars($row['challenges']) ?></p>
                                <p><strong>Lessons Learned:</strong> <?= htmlspecialchars($row['lessons']) ?></p>
                            </div>
                        <?php else: ?>
                            <p><strong>Task:</strong> <?= htmlspecialchars($row['task_name']) ?></p>
                            <p><strong>Description:</strong> <?= htmlspecialchars($row['task_desc']) ?></p>
                            <p><strong>Time:</strong> <?= date('g:i A', strtotime($row['start_time'])) ?> - <?= date('g:i A', strtotime($row['end_time'])) ?></p>
                            <p><strong>Status:</strong> 
                                <span class="badge <?= 
                                    $row['status'] === 'Completed' ? 'badge-success' : 
                                    ($row['status'] === 'In Progress' ? 'badge-warning' : 'badge-danger') 
                                ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </p>
                        <?php endif; ?>
                        
                        <div class="action-buttons">
                            <button onclick="deleteLog(<?= $row['id'] ?>)" class="btn btn-outline">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <div class="btn-group">
                    <button onclick="window.location.href='export_logs.php'" class="btn btn-outline">
                        <i class="fas fa-file-export"></i> Export All Logs
                    </button>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>No logs found</h3>
                    <p>Start by adding your first log above</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function openTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Deactivate all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Activate selected tab
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        
        function deleteLog(id) {
            if (confirm("Are you sure you want to delete this log?\nThis action cannot be undone.")) {
                window.location.href = `delete_log.php?id=${id}`;
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>