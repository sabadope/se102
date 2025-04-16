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