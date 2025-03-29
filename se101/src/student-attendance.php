<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Import fonts and reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #eee;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #F9F9F9;
            padding: 20px;
            position: fixed;
            height: 100%;
            transition: 0.3s;
        }

        .sidebar .brand {
            font-size: 22px;
            font-weight: 700;
            color: #3C91E6;
            display: flex;
            align-items: center;
        }

        .sidebar .menu {
            margin-top: 30px;
        }

        .sidebar .menu li {
            list-style: none;
            margin: 15px 0;
        }

        .sidebar .menu a {
            text-decoration: none;
            font-size: 16px;
            color: #333;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar .menu a:hover, .sidebar .menu .active {
            background: #CFE8FF;
            color: #3C91E6;
        }

        .sidebar .menu a i {
            margin-right: 10px;
            font-size: 20px;
        }

        /* Main content */
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
            transition: 0.3s;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header .search {
            display: flex;
            align-items: center;
            background: #eee;
            padding: 8px;
            border-radius: 20px;
        }

        .header .search input {
            border: none;
            outline: none;
            background: none;
            padding: 5px;
        }

        .header .profile {
            display: flex;
            align-items: center;
        }

        .header .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card i {
            font-size: 40px;
            color: #3C91E6;
        }

        .card h3 {
            font-size: 18px;
        }

        /* Recent Activity & Events */
        .details {
            margin-top: 20px;
            display: flex;
            gap: 20px;
        }

        .details .box {
            flex: 1;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .details .box h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .details .box ul {
            list-style: none;
        }

        .details .box ul li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
                padding: 10px;
            }

            .sidebar .brand,
            .sidebar .menu a span {
                display: none;
            }

            .sidebar .menu a i {
                font-size: 24px;
                margin-right: 0;
            }

            .main-content {
                margin-left: 60px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand"><i class='bx bxs-graduation'></i> Dashboard</div>
        <ul class="menu">
            <li><a href="#" class="active"><i class='bx bx-home'></i> <span>Home</span></a></li>
            <li><a href="#"><i class='bx bx-book'></i> <span>Courses</span></a></li>
            <li><a href="#"><i class='bx bx-calendar'></i> <span>Schedule</span></a></li>
            <li><a href="#"><i class='bx bx-message-square'></i> <span>Messages</span></a></li>
            <li><a href="#"><i class='bx bx-cog'></i> <span>Settings</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Header -->
        <div class="header">
            <div class="search">
                <i class='bx bx-search'></i>
                <input type="text" placeholder="Search...">
            </div>
            <div class="profile">
                <span>John Doe</span>
                <img src="https://via.placeholder.com/40" alt="Profile">
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <div>
                    <h3>Attendance</h3>
                    <p>85%</p>
                </div>
                <i class='bx bx-check-circle'></i>
            </div>
            <div class="card">
                <div>
                    <h3>Assignments</h3>
                    <p>5 Pending</p>
                </div>
                <i class='bx bx-task'></i>
            </div>
            <div class="card">
                <div>
                    <h3>Performance</h3>
                    <p>A Grade</p>
                </div>
                <i class='bx bx-bar-chart-alt'></i>
            </div>
        </div>

        <!-- Details -->
        <div class="details">
            <div class="box">
                <h3>Recent Activities</h3>
                <ul>
                    <li>Math Assignment Submitted</li>
                    <li>New Announcement in Science</li>
                    <li>Physics Quiz Results Out</li>
                </ul>
            </div>
            <div class="box">
                <h3>Upcoming Events</h3>
                <ul>
                    <li>Final Exams - July 20</li>
                    <li>Parent-Teacher Meeting - July 25</li>
                </ul>
            </div>
        </div>

    </div>

</body>
</html>
