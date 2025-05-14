<style>
    .nav-link {
        color: white;
        margin-right: 20px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        text-decoration: none;
        color:rgb(202, 199, 185);
    }

    .nav-link.active {
        font-weight: bold;
        text-decoration: none;
        color:rgb(202, 198, 179);
    }
</style>

<div style="position: fixed; top: 0; left: 0; right: 0; z-index: 999;">
    <nav style="background-color: #2c3e50; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: white; font-family: Arial, sans-serif; font-size: 18px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        
        <div>
            <a href="jv-dashboard.php" class="nav-link <?php echo ($activePage === 'dashboard') ? 'active' : ''; ?>">Dashboard</a>
            <a href="jv-supervisor_dashboard.php" class="nav-link <?php echo ($activePage === 'attendance') ? 'active' : ''; ?>">Attendance</a>
            
        </div>
    </nav>
</div>
