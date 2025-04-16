<?php
require_once '../src/config.php'; // Include DB connection

// Fetch the count of each role
$stmt = $pdo->prepare("
    SELECT role, COUNT(*) as count FROM users 
    WHERE role IN ('Student', 'Client') 
    GROUP BY role
");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize counts
$students_count = 0;
$supervisors_count = 0;
$clients_count = 0;

// Assign counts based on role
foreach ($roles as $role) {
    if ($role['role'] === 'Student') {
        $students_count = $role['count'];
    } elseif ($role['role'] === 'Client') {
        $clients_count = $role['count'];
    }
}

function timeAgo($timestamp) {
    $time_difference = time() - strtotime($timestamp); // Get time difference in seconds

    if ($time_difference < 60) {
        return "Just now"; // Less than a minute
    } elseif ($time_difference < 3600) {
        $minutes = floor($time_difference / 60);
        return $minutes . " min" . ($minutes > 1 ? "s" : "") . " ago"; // 1 min, 2 mins, etc.
    } elseif ($time_difference < 86400) {
        $hours = floor($time_difference / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago"; // 1 hour, 2 hours, etc.
    } else {
        $days = floor($time_difference / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago"; // 1 day, 2 days, etc.
    }
}

// Fetch the 4 most recent users
$stmt = $pdo->query("SELECT username, role, created_at FROM users ORDER BY created_at DESC LIMIT 4");
$recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the count of each user role
$stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users WHERE role NOT IN ('Admin', 'Supervisor') GROUP BY role");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$roles = [];
$counts = [];

foreach ($users as $user) {
    $roles[] = $user['role'];
    $counts[] = $user['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/dashboard.css">

	<!-- My CSS -->
	<style>
		/* Target the entire page's scrollbar */
        ::-webkit-scrollbar {
            width: 6px; /* Set the width of the scrollbar */
            height: 6px; /* Set the height of the horizontal scrollbar (if needed) */
        }

        /* Style the track (the background of the scrollbar) */
        ::-webkit-scrollbar-track {
            background: #f1f1f1; /* Light background for the track */
            border-radius: 10px;
        }

        /* Style the thumb (the draggable part of the scrollbar) */
        ::-webkit-scrollbar-thumb {
            background: #888; /* Set the color of the thumb */
            border-radius: 10px; /* Round corners for the thumb */
        }

        /* Hover effect for the thumb */
        ::-webkit-scrollbar-thumb:hover {
            background: #555; /* Darker color when the user hovers over the thumb */
        }

        html {
            overflow-x: hidden;
        }

        body.dark {
            --light: #0C0C1E;
            --grey: #060714;
            --dark: #FBFBFB;
        }

        body {
            background: var(--grey);
            overflow-x: hidden;
        }

        /* ========== SIDEBAR BASE ========== */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100%;
            background: var(--light);
            z-index: 2000;
            font-family: var(--lato);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease-in-out;
            overflow: hidden;
            scrollbar-width: none;
        }

        #sidebar::--webkit-scrollbar {
            display: none;
        }

        #sidebar.hide {
            width: 60px;
        }

        /* ========== BRAND ========== */
        #sidebar .brand {
            font-size: 24px;
            font-weight: 700;
            height: 56px;
            display: flex;
            align-items: center;
            color: var(--blue);
            position: sticky;
            top: 0;
            left: 0;
            background: var(--light);
            z-index: 500;
            box-sizing: content-box;
        }

        #sidebar .brand .bx {
            min-width: 60px;
            display: flex;
            justify-content: center;
        }

        /* ========== SIDEBAR CONTENT WRAPPER ========== */
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 0;
        }

        /* ========== SIDE MENU BASE ========== */
        #sidebar .side-menu {
            width: 100%;
            margin-top: 48px;
        }

        #sidebar .side-menu li {
            height: 48px; /* or whatever height you set */
            background: transparent;
            margin-left: 6px;
            border-radius: 48px 0 0 48px;
            padding: 4px;
            transition: margin-top 0.2s ease; /* Smooth transition for margin adjustment */
        }

        #sidebar .side-menu li a {
            width: 100%;
            height: 100%;
            background: var(--light);
            display: flex;
            align-items: center;
            border-radius: 48px;
            font-size: 16px;
            color: var(--dark);
            white-space: nowrap;

        }

        #sidebar .side-menu li a .bx {
            min-width: calc(60px - ((4px + 6px) * 2));
            display: flex;
            justify-content: center;
        }

        /* ========== ACTIVE STATE ========== */
        #sidebar .side-menu li.active {
            background: var(--grey);
            position: relative;

        }

        /* ===== Submenu Default Style ===== */
        #sidebar .side-menu .sub-menu li a {
            padding-left: 1px;       
            transition: color 0.3s;
        }



        #sidebar .side-menu li.active::before,
        #sidebar .side-menu li.active::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            right: 0;
            z-index: -1;
        }

        #sidebar .side-menu li.active::before {
            top: -40px;
            box-shadow: 20px 20px 0 var(--grey);

        }

        #sidebar .side-menu li.active::after {
            bottom: -40px;
            box-shadow: 20px -20px 0 var(--grey);
        }

        #sidebar .side-menu.top li.active a {
            color: var(--blue);
        }

        /* ========== HOVER & HIDE EFFECTS ========== */
        #sidebar .side-menu.top li a:hover {
            color: var(--blue);
        }

        #sidebar.hide .side-menu li a {
            width: calc(48px - (4px * 2));
            transition: width 0.3s ease;
        }

        /* Collapsed state */
        #sidebar.hide .side-menu li a .text {
            opacity: 0;
            visibility: hidden;
            width: 0;
            overflow: hidden;
            white-space: nowrap;
            transition: all 0.3s ease;
            margin: 0;
            padding: 0;
        }

        /* Expanded state */
        #sidebar .side-menu li a .text {
            opacity: 1;
            visibility: visible;
            width: auto;
            
            transition: all 0.3s ease;
        }

        /* ========== LOGOUT COLOR ========== */
        #sidebar .side-menu li a.logout {
            color: var(--red);
        }

        #sidebar .side-menu li a.logout:hover {
            color: #bb2d3b;
        }

        /* ========== SUBMENU DROPDOWN ========== */
        .sub-menu {
            display: none;
            padding-left: 1.5rem;
            transition: all 0.3s ease;

        }

        /* Optional: Add active styles for the expanded submenu */
        .sub-menu.active {
            display: block; /* Ensure the submenu is visible */
        }

        /* Apply grey background to active items */
        .has-submenu.active > a {
            background: var(--grey);
            border-radius: 5px; /* Ensure the border radius is maintained */
        }

        /* Optionally, add hover effect to the active link */
        .has-submenu.active > a:hover {
            background: var(--grey); /* Active background for the Activities tab */
            border-radius: 5px; /* Preserve the border radius */
        }

        .has-submenu.active .sub-menu {
            display: block;
        }

        /* ========== Arrow for expanded/collapsed state ========== */
        .has-submenu > a .arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
            transform: rotate(0deg); /* Default: collapsed (arrow up) */
        }

        /* Arrow rotation for expanded submenu (handled by JavaScript now) */
        .has-submenu.active > a .arrow {
            transform: rotate(180deg); /* Expanded: arrow down */
        }

        


        /* SIDEBAR */


        /* CONTENT */
        #content {
            position: relative;
            width: calc(100% - 280px);
            left: 280px;
            transition: .3s ease;
        }
        #sidebar.hide ~ #content {
            width: calc(100% - 60px);
            left: 60px;
        }

        /* NAVBAR */
        #content nav {
            height: 56px;
            background: var(--light);
            padding: 0 24px;
            display: flex;
            align-items: center;
            grid-gap: 24px;
            font-family: var(--lato);
            position: sticky;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        #content nav::before {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            bottom: -40px;
            left: 0;
            border-radius: 50%;
            box-shadow: -20px -20px 0 var(--light);
        }
        #content nav a {
            color: var(--dark);
        }
        #content nav .bx.bx-menu {
            cursor: pointer;
            color: var(--dark);
        }
        #content nav .nav-link {
            font-size: 16px;
            transition: .3s ease;
        }
        #content nav .nav-link:hover {
            color: var(--blue);
        }
        #content nav form {
            max-width: 400px;
            width: 100%;
            margin-right: auto;
        }
        #content nav form .form-input {
            display: flex;
            align-items: center;
            height: 36px;
        }
        #content nav form .form-input input {
            flex-grow: 1;
            padding: 0 16px;
            height: 100%;
            border: none;
            background: var(--grey);
            border-radius: 36px 0 0 36px;
            outline: none;
            width: 100%;
            color: var(--dark);
        }
        #content nav form .form-input button {
            width: 36px;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--blue);
            color: var(--light);
            font-size: 18px;
            border: none;
            outline: none;
            border-radius: 0 36px 36px 0;
            cursor: pointer;
        }
        #content nav .notification {
            font-size: 20px;
            position: relative;
        }
        #content nav .notification .num {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid var(--light);
            background: var(--red);
            color: var(--light);
            font-weight: 700;
            font-size: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #content nav .profile img {
            width: 36px;
            height: 36px;
            object-fit: cover;
            border-radius: 50%;
        }
        #content nav .switch-mode {
            display: block;
            min-width: 50px;
            height: 25px;
            border-radius: 25px;
            background: var(--grey);
            cursor: pointer;
            position: relative;
        }
        #content nav .switch-mode::before {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            bottom: 2px;
            width: calc(25px - 4px);
            background: var(--blue);
            border-radius: 50%;
            transition: all .3s ease;
        }
        #content nav #switch-mode:checked + .switch-mode::before {
            left: calc(100% - (25px - 4px) - 2px);
        }

        nav.navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #fff; /* Optional */
            position: relative;
        }

        /* Force the left and right parts to occupy equal width for balance */
        .nav-left,
        .nav-right {
            flex: 1;
            display: flex;
            align-items: center;

        }

        /* Right section spacing */
        .nav-right {
            justify-content: flex-end;
            gap: 30px;
            margin-left: 16%;
        }

        /* Center part (search bar) stays in the middle */
        .nav-center {
            flex: 0 0 auto;
            display: flex;
            justify-content: space-between;
        }

        /* Search form styling */
        .form-input {
            display: flex;
            align-items: center;
            background: #f1f1f1;
            padding: 0;
            border-radius: 20px;
            width: 100%;
        }

        .form-input input[type="search"] {
            border: none;
            outline: none;
            background: transparent;
            padding: 5px 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;

        }

        .search-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #333;
        }

        /* Profile image */
        .profile img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

	</style>

	<title>Supervisor Dashboard</title>
</head>
<body>


	
	 <!-- SIDEBAR -->
	 <section id="sidebar">
        <a href="#" class="brand">
		<i class="bx bxs-briefcase"></i>
            <span class="text">Supervisor Panel</span>
        </a>

        <!-- NEW FLEX WRAPPER -->
        <div class="sidebar-content">
            <!-- TOP ITEMS -->
            <ul class="side-menu top">
                <li class="active">
                    <a href="student-dashboard.php">
                        <i class='bx bxs-dashboard'></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="student-attendance.php">
                        <i class='bx bxs-calendar-check'></i>
                        <span class="text">Attendance</span>
                    </a>
                </li>
                <li>
                    <a href="student-messages.php">
                        <i class='bx bxs-message-dots'></i>
                        <span class="text">Message</span>
                    </a>
                </li>
                <li>
                    <a href="student-performance.php" style="display: flex; align-items: center;">
                        <i class='bx bxs-book-content'></i>
                        <span class="text">Performance</span>
                        <i class='bx bx-chevron-down arrow' style="margin-left: auto;"></i>
                    </a>
                </li>

                <li>
                    <a href="student-activities.php" style="display: flex; align-items: center;">
                        <i class='bx bxs-folder-open'></i>
                        <span class="text">Activities</span>
                        <i class='bx bx-chevron-down arrow' style="margin-left: auto;"></i>
                    </a>
                </li>
                <li>
                    <a href="student-settings.php">
                        <i class='bx bxs-cog'></i>
                        <span class="text">Settings</span>
                    </a>
                </li>
                <li>
                    <a href="student-logout.php" class="logout">
                        <i class='bx bxs-log-out-circle'></i>
                        <span class="text">Logout</span>
                    </a>
                </li>
            </ul>

        </div>
    </section>

	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
        <nav class="navbar">
            <i class="bx bx-chevron-left" style="font-size: 25px;"></i> <!-- Sidebar toggle button -->
            <!-- Left Spacer -->
            <div class="nav-left"></div>

            <!-- Center: Search Form -->
            <form action="#" class="nav-center">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button type="submit" class="search-btn">
                        <i class='bx bx-search'></i>
                    </button>
                </div>
            </form>

            <!-- Right: Icons -->
            <div class="nav-right">
                <input type="checkbox" id="switch-mode" hidden>
                <label for="switch-mode" class="switch-mode"></label>
                <a href="#" class="notification">
                    <i class='bx bxs-bell'></i>
                    <span class="num">8</span>
                </a>
                <a href="#" class="profile">
                    <img src="img/people.png">
                </a>
            </div>
        </nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Dashboard</h1>
					<ul class="breadcrumb">
						<li>
							<a href="admin-dashboard.php">Home</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active">Dashboard</a>
						</li>
						
					</ul>
				</div>
				<a href="#" class="btn-download">
					<i class='bx bxs-cloud-download' ></i>
					<span class="text">Download PDF</span>
				</a>
			</div>

			<ul class="box-info">
			    <li>
			        <i class='bx bxs-group'></i>
			        <span class="text">
			            <h3><?php echo $students_count; ?></h3>
			            <p>Students</p>
			        </span>
			    </li>
			    <li>
			        <i class='bx bxs-group'></i>
			        <span class="text">
			            <h3><?php echo $clients_count; ?></h3>
			            <p>Clients</p>
			        </span>
			    </li>
			</ul>


			<div class="table-data">
			    <div class="order">
			        <div class="head">
			            <h3 id="section-title">Recent Accounts</h3> <!-- Title Changes Dynamically -->
			            <i class='bx bx-filter'></i>
			            <i class='bx bxs-pie-chart-alt-2 chart-toggle'></i> <!-- Toggle Button -->
			        </div>

			        <!-- Recent Accounts Container (Holds BOTH Table & Chart) -->
			        <div class="recent-accounts">
			            <!-- Table View (Default Display) -->
			            <div class="table-view">
			                <table>
			                    <thead>
			                        <tr>
			                            <th>User Name</th>
			                            <th>Role</th>
			                            <th>Status</th>
			                        </tr>
			                    </thead>
			                    <tbody>
			                        <?php foreach ($recent_users as $user): ?>
			                            <tr>
			                                <td>
			                                    <img src="img/people.png">
			                                    <p><?= htmlspecialchars($user['username']) ?></p>
			                                </td>
			                                <td><?= htmlspecialchars($user['role']) ?></td>
			                                <td class="registered-time" data-time="<?= htmlspecialchars($user['created_at']) ?>"></td>
			                            </tr>
			                        <?php endforeach; ?>
			                    </tbody>
			                </table>
			            </div>

			            <!-- Pie Chart View (Hidden by Default) -->
			            <div class="chart-container">
			                <canvas id="userChart"></canvas>
			            </div>
			        </div>
			    </div>
				
				<div class="todo">
					<div class="head">
						<h3>Todos</h3>
						<i class='bx bx-plus' ></i>
						<i class='bx bx-filter' ></i>
					</div>
					<ul class="todo-list">
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="not-completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="not-completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
					</ul>
				</div>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	

	<!-- NAV BAR W/ TOGGLE HIDE -->
    <script>        
        // Select all sidebar menu items
        const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

        allSideMenu.forEach(item => {
            const li = item.parentElement;

            item.addEventListener('click', function () {
                allSideMenu.forEach(i => {
                    i.parentElement.classList.remove('active');
                });
                li.classList.add('active');
            });
        });

        // TOGGLE SIDEBAR
        const menuBar = document.querySelector('#content nav .bx.bx-chevron-left'); // Updated selector
        const sidebar = document.getElementById('sidebar');

        menuBar.addEventListener('click', function () {
            sidebar.classList.toggle('hide');

            // Toggle the icon between left and right chevron + add rotation animation
            if (sidebar.classList.contains('hide')) {
                menuBar.classList.replace('bx-chevron-left', 'bx-chevron-right');
            } else {
                menuBar.classList.replace('bx-chevron-right', 'bx-chevron-left');
            }

            // Add rotation animation
            menuBar.classList.add('rotate-icon');
            setTimeout(() => {
                menuBar.classList.remove('rotate-icon'); // Remove class after animation completes
            }, 300); // Matches the CSS transition time
        });

        // SEARCH TOGGLE (For small screens)
        const searchButton = document.querySelector('#content nav form .form-input button');
        const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
        const searchForm = document.querySelector('#content nav form');

        searchButton.addEventListener('click', function (e) {
            if (window.innerWidth < 576) {
                e.preventDefault();
                searchForm.classList.toggle('show');
                if (searchForm.classList.contains('show')) {
                    searchButtonIcon.classList.replace('bx-search', 'bx-x');
                } else {
                    searchButtonIcon.classList.replace('bx-x', 'bx-search');
                }
            }
        });
    </script>


    <!-- SIDE BAR SCRIPT -->
    <script>
        // ========== DEFAULT ACTIVATION RULES FOR ACTIVITIES & PERFORMANCE ==========

        const path = window.location.pathname;

        if (path.includes("student-activities.php") || path.includes("student-performance.php")) {
            const menuId = path.includes("student-performance.php") ? '#performance-submenu' : '#activities-submenu';
            const menuElement = document.querySelector(menuId);
            const submenu = menuElement.querySelector('.sub-menu');
            const nextLi = menuElement.nextElementSibling;

            // Keep the tab styled as active
            menuElement.classList.add('active');

            // Keep the submenu expanded
            submenu.classList.add('active');
            submenu.style.display = 'block';

            // Push down the next item to avoid overlap
            if (nextLi) nextLi.style.marginTop = '90px';
        }

        // ========== SUBMENU TOGGLE FUNCTIONALITY ==========

        const submenuLinks = document.querySelectorAll('.has-submenu > a');

        submenuLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent redirect

                const parentLi = link.parentElement;
                const submenu = parentLi.querySelector('.sub-menu');
                const arrow = link.querySelector('.arrow'); // Get the arrow element
                const nextLi = parentLi.nextElementSibling;

                // Toggle submenu visibility
                const isExpanded = submenu.classList.contains('active');
                submenu.classList.toggle('active');
                submenu.style.display = isExpanded ? 'none' : 'block';

                // Rotate arrow based on expanded/collapsed state
                arrow.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';

                // Adjust margin of next item
                if (nextLi) nextLi.style.marginTop = isExpanded ? '0px' : '180px';
            });
        });

        // ========== HIGHLIGHT ACTIVE SUBMENU ITEM ==========
        const subLinks = document.querySelectorAll('.sub-menu li a');
        subLinks.forEach(link => {
            if (window.location.href.includes(link.getAttribute('href'))) {
                link.classList.add('active');
            }
        });
    </script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Load Chart.js -->
</body>
</html>