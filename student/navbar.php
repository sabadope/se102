<nav>
    <div class="nav-brand">
        <a href="dashboard.php">Feedback System</a>
    </div>
    <div class="nav-links">
        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_panel.php">Admin Panel</a>
                <a href="manage_users.php">Manage Users</a>
            <?php elseif ($_SESSION['role'] === 'supervisor'): ?>
                <a href="supervisor_feedback.php">Give Feedback</a>
                <a href="intern_progress.php">Intern Progress</a>
            <?php elseif ($_SESSION['role'] === 'customer'): ?>
                <a href="customer_feedback.php">Give Feedback</a>
            <?php elseif ($_SESSION['role'] === 'intern'): ?>
                <a href="intern_dashboard.php">My Dashboard</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>