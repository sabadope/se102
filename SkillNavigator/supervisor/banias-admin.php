<?php
session_start();
require_once 'banias-db_connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: banias-login.php');
    exit();
}

// Get total users count
$query = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
$result = $conn->query($query);
$total_users = $result->fetch_assoc()['total_users'];

// Get total logs count
$query = "SELECT COUNT(*) as total_logs FROM logs";
$result = $conn->query($query);
$total_logs = $result->fetch_assoc()['total_logs'];

// Get recent logs
$query = "SELECT l.*, u.username, u.full_name 
          FROM logs l 
          JOIN users u ON l.user_id = u.id 
          ORDER BY l.timestamp DESC 
          LIMIT 5";
$recent_logs = $conn->query($query);

// Get recent users
$query = "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5";
$recent_users = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background: white;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .sidebar-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            color: var(--secondary);
            font-size: 0.875rem;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--secondary);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .nav-link:hover, .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 260px;
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

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-menu .btn {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .stat-card h3 {
            color: var(--secondary);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .stat-card .value {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--dark);
        }

        /* Recent Activity Section */
        .recent-activity {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header h2 {
            font-size: 1.25rem;
            color: var(--dark);
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            padding: 1rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item h4 {
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .activity-item p {
            color: var(--secondary);
            font-size: 0.875rem;
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

        .view-all {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .view-all:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
            }
        }

        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2 style="text-align: center;">Supervisor Panel</h2>
                
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="banias-admin.php" class="nav-link active">
                        <i class="fas fa-home"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="banias-admin_users.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="banias-admin_logs.php" class="nav-link">
                        <i class="fas fa-clipboard-list"></i>
                        Logs
                    </a>
                </li>
                <li class="nav-item">
                    <a href="banias-admin_settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a href="banias-logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Dashboard Overview</h1>
                
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="value"><?= $total_users ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Logs</h3>
                    <div class="value"><?= $total_logs ?></div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="recent-activity">
                <div class="section-header">
                    <h2>Recent Logs</h2>
                    <a href="banias-admin_logs.php" class="view-all">View All</a>
                </div>
                <ul class="activity-list">
                    <?php while ($log = $recent_logs->fetch_assoc()): ?>
                        <li class="activity-item">
                            <h4><?= htmlspecialchars($log['task_name']) ?></h4>
                            <p>
                                By <?= htmlspecialchars($log['username']) ?> • 
                                <?= date('M j, Y', strtotime($log['timestamp'])) ?> •
                                <span class="status-badge status-<?= strtolower($log['status']) ?>">
                                    <?= ucfirst($log['status']) ?>
                                </span>
                            </p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Recent Users -->
            <div class="recent-activity">
                <div class="section-header">
                    <h2>Recent Users</h2>
                    <a href="banias-admin_users.php" class="view-all">View All</a>
                </div>
                <ul class="activity-list">
                    <?php while ($user = $recent_users->fetch_assoc()): ?>
                        <li class="activity-item">
                            <h4><?= htmlspecialchars($user['username']) ?></h4>
                            <p>
                                <?= htmlspecialchars($user['full_name']) ?> • 
                                <?= htmlspecialchars($user['email']) ?> •
                                Joined <?= date('M j, Y', strtotime($user['created_at'])) ?>
                            </p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
</html>