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

	<!-- My CSS -->
	<style>
		
		@import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap');

		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		a {
			text-decoration: none;
		}

		li {
			list-style: none;
		}

		:root {
			--poppins: 'Poppins', sans-serif;
			--lato: 'Lato', sans-serif;

			--light: #F9F9F9;
			--blue: #3C91E6;
			--light-blue: #CFE8FF;
			--grey: #eee;
			--dark-grey: #AAAAAA;
			--dark: #342E37;
			--red: #DB504A;
			--yellow: #FFCE26;
			--light-yellow: #FFF2C6;
			--orange: #FD7238;
			--light-orange: #FFE0D3;
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

		/* SIDEBAR */
		#sidebar {
			position: fixed;
			top: 0;
			left: 0;
			width: 280px;
			height: 100%;
			background: var(--light);
			z-index: 2000;
			font-family: var(--lato);
			transition: all 0.3s ease-in-out;
			overflow-x: hidden;
			scrollbar-width: none;
		}
		#sidebar::--webkit-scrollbar {
			display: none;
		}
		#sidebar.hide {
			width: 60px;

		}
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
		#sidebar .side-menu {
			width: 100%;
			margin-top: 48px;
		}
		#sidebar .side-menu li {
			height: 48px;
			background: transparent;
			margin-left: 6px;
			border-radius: 48px 0 0 48px;
			padding: 4px;
		}
		#sidebar .side-menu li.active {
			background: var(--grey);
			position: relative;
		}
		#sidebar .side-menu li.active::before {
			content: '';
			position: absolute;
			width: 40px;
			height: 40px;
			border-radius: 50%;
			top: -40px;
			right: 0;
			box-shadow: 20px 20px 0 var(--grey);
			z-index: -1;
		}
		#sidebar .side-menu li.active::after {
			content: '';
			position: absolute;
			width: 40px;
			height: 40px;
			border-radius: 50%;
			bottom: -40px;
			right: 0;
			box-shadow: 20px -20px 0 var(--grey);
			z-index: -1;
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
			overflow-x: hidden;
		}
		#sidebar .side-menu.top li.active a {
			color: var(--blue);
		}
		#sidebar.hide .side-menu li a {
			width: calc(48px - (4px * 2));
			transition: width .3s ease;
		}
		#sidebar .side-menu li a.logout {
			color: var(--red);
		}
		#sidebar .side-menu.top li a:hover {
			color: var(--blue);
		}
		#sidebar .side-menu li a .bx {
			min-width: calc(60px  - ((4px + 6px) * 2));
			display: flex;
			justify-content: center;
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
		/* NAVBAR */





		/* MAIN */
		#content main {
			width: 100%;
			padding: 36px 24px;
			font-family: var(--poppins);
			max-height: calc(100vh - 56px);
			overflow-y: auto;
		}
		#content main .head-title {
			display: flex;
			align-items: center;
			justify-content: space-between;
			grid-gap: 16px;
			flex-wrap: wrap;
		}
		#content main .head-title .left h1 {
			font-size: 36px;
			font-weight: 600;
			margin-bottom: 10px;
			color: var(--dark);
		}
		#content main .head-title .left .breadcrumb {
			display: flex;
			align-items: center;
			grid-gap: 16px;
		}
		#content main .head-title .left .breadcrumb li {
			color: var(--dark);
		}
		#content main .head-title .left .breadcrumb li a {
			color: var(--dark-grey);
			
		}
		#content main .head-title .left .breadcrumb li a.active {
			color: var(--blue);
			pointer-events: unset;
		}
		#content main .head-title .btn-download {
			height: 36px;
			padding: 0 16px;
			border-radius: 36px;
			background: var(--blue);
			color: var(--light);
			display: flex;
			justify-content: center;
			align-items: center;
			grid-gap: 10px;
			font-weight: 500;
		}




		#content main .box-info {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
			grid-gap: 24px;
			margin-top: 24px;
		}
		#content main .box-info li {
			padding: 24px;
			background: var(--light);
			border-radius: 20px;
			display: flex;
			align-items: center;
			grid-gap: 24px;
		}
		#content main .box-info li .bx {
			width: 80px;
			height: 80px;
			border-radius: 10px;
			font-size: 36px;
			display: flex;
			justify-content: center;
			align-items: center;
		}
		#content main .box-info li:nth-child(1) .bx {
			background: var(--light-blue);
			color: var(--blue);
		}
		#content main .box-info li:nth-child(2) .bx {
			background: var(--light-yellow);
			color: var(--yellow);
		}
		#content main .box-info li:nth-child(3) .bx {
			background: var(--light-orange);
			color: var(--orange);
		}
		#content main .box-info li .text h3 {
			font-size: 24px;
			font-weight: 600;
			color: var(--dark);
		}
		#content main .box-info li .text p {
			color: var(--dark);	
		}





		#content main .table-data {
			display: flex;
			flex-wrap: wrap;
			grid-gap: 24px;
			margin-top: 24px;
			width: 100%;
			color: var(--dark);
		}
		#content main .table-data > div {
			border-radius: 20px;
			background: var(--light);
			padding: 24px;
			overflow-x: auto;
		}
		#content main .table-data .head {
			display: flex;
			align-items: center;
			grid-gap: 16px;
			margin-bottom: 24px;
		}
		#content main .table-data .head h3 {
			margin-right: auto;
			font-size: 24px;
			font-weight: 600;
		}
		#content main .table-data .head .bx {
			cursor: pointer;
		}

		#content main .table-data .order {
			flex-grow: 1;
			flex-basis: 500px;
		}
		#content main .table-data .order table {
			width: 100%;
			border-collapse: collapse;
		}
		#content main .table-data .order table th {
			padding-bottom: 12px;
			font-size: 13px;
			text-align: left;
			border-bottom: 1px solid var(--grey);
		}
		#content main .table-data .order table td {
			padding: 16px 0;
		}
		#content main .table-data .order table tr td:first-child {
			display: flex;
			align-items: center;
			grid-gap: 12px;
			padding-left: 6px;
		}
		#content main .table-data .order table td img {
			width: 36px;
			height: 36px;
			border-radius: 50%;
			object-fit: cover;
		}
		#content main .table-data .order table tbody tr:hover {
			background: var(--grey);
		}
		#content main .table-data .order table tr td .status {
			font-size: 10px;
			padding: 6px 16px;
			color: var(--light);
			border-radius: 20px;
			font-weight: 700;
		}
		#content main .table-data .order table tr td .status.completed {
			background: var(--blue);
		}
		#content main .table-data .order table tr td .status.process {
			background: var(--yellow);
		}
		#content main .table-data .order table tr td .status.pending {
			background: var(--orange);
		}


		#content main .table-data .todo {
			flex-grow: 1;
			flex-basis: 300px;
		}
		#content main .table-data .todo .todo-list {
			width: 100%;
		}
		#content main .table-data .todo .todo-list li {
			width: 100%;
			margin-bottom: 16px;
			background: var(--grey);
			border-radius: 10px;
			padding: 14px 20px;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		#content main .table-data .todo .todo-list li .bx {
			cursor: pointer;
		}
		#content main .table-data .todo .todo-list li.completed {
			border-left: 10px solid var(--blue);
		}
		#content main .table-data .todo .todo-list li.not-completed {
			border-left: 10px solid var(--orange);
		}
		#content main .table-data .todo .todo-list li:last-child {
			margin-bottom: 0;
		}
		/* MAIN */
		/* CONTENT */









		@media screen and (max-width: 768px) {
			#sidebar {
				width: 200px;
			}

			#content {
				width: calc(100% - 60px);
				left: 200px;
			}

			#content nav .nav-link {
				display: none;
			}
		}






		@media screen and (max-width: 576px) {
			#content nav form .form-input input {
				display: none;
			}

			#content nav form .form-input button {
				width: auto;
				height: auto;
				background: transparent;
				border-radius: none;
				color: var(--dark);
			}

			#content nav form.show .form-input input {
				display: block;
				width: 100%;
			}
			#content nav form.show .form-input button {
				width: 36px;
				height: 100%;
				border-radius: 0 36px 36px 0;
				color: var(--light);
				background: var(--red);
			}

			#content nav form.show ~ .notification,
			#content nav form.show ~ .profile {
				display: none;
			}

			#content main .box-info {
				grid-template-columns: 1fr;
			}

			#content main .table-data .head {
				min-width: 420px;
			}
			#content main .table-data .order table {
				min-width: 420px;
			}
			#content main .table-data .todo .todo-list {
				min-width: 420px;
			}
		}

		/* Ensure the container remains fixed in size */
	    .recent-accounts {
	        width: 100%;
	        max-width: 100%;
	        height: 330px; /* Fixed height to prevent stretching */
	        overflow: hidden; /* Prevent content from overflowing */
	        position: relative;
	    }

	    /* Table and Chart Container */
	    .table-view, .chart-container {
	        width: 100%;
	        height: 100%;
	        display: flex;
	        align-items: center;
	        justify-content: center;
	        position: absolute;
	        top: 0;
	        left: 0;
	        
	    }

	    /* Hide Pie Chart Initially */
	    .chart-container {
	        display: none;
	        
	    }

	    /* Ensure the Pie Chart adjusts inside the container */
	    canvas {
	        max-width: 100% !important;
	        max-height: 100% !important;
	        
	    }
	</style>

	<title>Supervisor Dashboard</title>
</head>
<body>


	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx bxs-user'></i>
			<span class="text">Super Panel</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="supervisor-dashboard.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="#">
					<i class='bx bxs-check-circle' ></i>
					<span class="text">Task</span>
				</a>
			</li>
			<li>
				<a href="#">
					<i class='bx bxs-doughnut-chart' ></i>
					<span class="text">Analytics</span>
				</a>
			</li>
			<li id="openChat">
			    <a href="supervisor-messages.php">
			        <i class='bx bxs-message-dots'></i>
			        <span class="text">Messages</span>
			    </a>
			</li>
			<li>
				<a href="#">
					<i class='bx bxs-group' ></i>
					<span class="text">Team</span>
				</a>
			</li>
		</ul>
		<ul class="side-menu">
			<li>
				<a href="#">
					<i class='bx bxs-cog' ></i>
					<span class="text">Settings</span>
				</a>
			</li>
			<li>
				<a href="supervisor-logout.php" class="logout">
					<i class='bx bxs-log-out-circle' ></i>
					<span class="text">Logout</span>
				</a>
			</li>
		</ul>
	</section>
	<!-- SIDEBAR -->



	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class="bx bx-chevron-left" style="font-size: 25px;"></i> <!-- Sidebar toggle button -->
			
			<form action="#">
				<div class="form-input">
					<input type="search" placeholder="Search...">
					<button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
				</div>
			</form>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
			<a href="#" class="notification">
				<i class='bx bxs-bell' ></i>
				<span class="num">8</span>
			</a>
			<a href="#" class="profile">
				<img src="img/people.png">
			</a>
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

	<!-- JavaScript for Toggle, Title Update & Pie Chart -->
	<script>
	    // Function to calculate "time ago"
	    function timeAgo(time) {
	        const now = new Date();
	        const createdAt = new Date(time);
	        const diff = Math.floor((now - createdAt) / 1000); // Time difference in seconds

	        if (diff < 60) {
	            return "Just now";
	        } else if (diff < 3600) {
	            return Math.floor(diff / 60) + " min ago";
	        } else if (diff < 86400) {
	            return Math.floor(diff / 3600) + " hour ago";
	        } else {
	            return Math.floor(diff / 86400) + " day ago";
	        }
	    }

	    // Function to update time dynamically
	    function updateTimes() {
	        document.querySelectorAll('.registered-time').forEach(el => {
	            const time = el.getAttribute('data-time');
	            el.textContent = timeAgo(time);
	        });
	    }

	    // Initial call & update every 30 seconds
	    updateTimes();
	    setInterval(updateTimes, 30000);

	    // Wait for the page to load
	    document.addEventListener("DOMContentLoaded", function () {
	        var chartToggle = document.querySelector(".chart-toggle");
	        var sectionTitle = document.getElementById("section-title");
	        var tableView = document.querySelector(".table-view");
	        var chartContainer = document.querySelector(".chart-container");

	        // Pie Chart Configuration
	        var ctx = document.getElementById('userChart').getContext('2d');
	        var userChart = new Chart(ctx, {
			    type: 'pie',
			    data: {
			        labels: <?php echo json_encode($roles); ?>,
			        datasets: [{
			            data: <?php echo json_encode($counts); ?>,
			            backgroundColor: ['#ff6384', '#36a2eb', '#ffcd56', '#4bc0c0']
			        }]
			    },
			    options: {
			        responsive: true,
			        layout: {
			            padding: {
			                bottom: 35 // Adds space between the chart and labels
			            }
			        },
			        plugins: {
			            legend: {
			                position: 'left',
			                labels: {
			                    boxWidth: 15, // Smaller legend boxes
			                    padding: 40,  // Adds spacing between legend items
			                }
			            }
			        }
			    }
			});

	        // Toggle between Table and Pie Chart
	        chartToggle.addEventListener("click", function () {
	            if (tableView.style.display === "none") {
	                tableView.style.display = "flex";
	                chartContainer.style.display = "none";
	                sectionTitle.textContent = "Recent Accounts"; // Update title back
	            } else {
	                tableView.style.display = "none";
	                chartContainer.style.display = "flex";
	                sectionTitle.textContent = "Total Users"; // Update title to Pie Chart
	            }
	        });
	    });
	</script>

	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Load Chart.js -->
</body>
</html>