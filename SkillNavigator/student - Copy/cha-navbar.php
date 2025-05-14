<nav>
    
    <div class="nav-links">
        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="cha-admin_panel.php">Admin Panel</a>
                <a href="cha-manage_users.php">Manage Users</a>
            <?php elseif ($_SESSION['role'] === 'supervisor'): ?>
                <a href="cha-supervisor_feedback.php">Give Feedback</a>
                <a href="cha-intern_progress.php">Intern Progress</a>
            <?php elseif ($_SESSION['role'] === 'customer'): ?>
                <a href="cha-customer_feedback.php">Give Feedback</a>
            <?php elseif ($_SESSION['role'] === 'intern'): ?>
                <a href="cha-intern_dashboard.php">My Dashboard</a>
            <?php endif; ?>
            
        <?php else: ?>
            <a href="cha-login.php">Login</a>
            <a href="cha-register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>