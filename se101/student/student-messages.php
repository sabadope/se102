<?php
session_start();
require_once '../src/config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    die("Unauthorized");
}

$currentUserEmail = $_SESSION['email']; // supervisor@gmail.com or client@gmail.com
$selectedUserEmail = $_POST['selected_user_email'] ?? null;

if (!$selectedUserEmail) {
    die("No user selected");
}

// Use the actual column name from your DB: `timestamp` or `sent_at`
$stmt = $pdo->prepare("
    SELECT * FROM messages
    WHERE (sender = :sender AND receiver = :receiver)
       OR (sender = :receiver AND receiver = :sender)
    ORDER BY timestamp ASC
");

$stmt->execute([
    'sender' => $currentUserEmail,
    'receiver' => $selectedUserEmail
]);

$chatHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return as JSON if using fetch/AJAX
header('Content-Type: application/json');
echo json_encode($chatHistory);

$stmt = $pdo->prepare("
    SELECT * FROM messages 
    WHERE sender = 'supervisor@gmail.com' AND receiver = :receiver 
    ORDER BY timestamp ASC LIMIT 0, 25
");
$stmt->execute(['receiver' => 'student1@gmail.com']);

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
            grid-gap: 0px;
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
            flex-grow: 1;
            flex-basis: 500px;
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
        

        /* Chat Container - Flex for Layout */
        .chat-container {
            display: flex;
            width: 100%; /* Matches chat-input width */
            max-width: 100%;
            height: 400px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            transition: width 0.3s ease-in-out;
        }

        /* Chat Content - Takes Full Space Initially */
        .chat-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease-in-out; /* Animates resizing */
        }

        /* Chat Header - Shows Chat Person */
        .chat-header {
            padding: 10px;
            background: #f1f1f1;
            border-bottom: 1px solid #ddd;
            text-align: center;
            font-weight: bold;
        }

        /* Chat Box - Scrollable */
        .chat-box {
            flex: 1;
            padding: 12px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* Message Styling */
        .message {
            background: #f1f1f1;
            padding: 10px;
            border-radius: 8px;
            max-width: 75%;
            word-wrap: break-word;
            position: relative;
            margin-bottom: 2px; /* Small space between messages */
        }

        /* Received Messages (Left Aligned) */
        .message.received {
            background: #ddd;
            align-self: flex-start;
        }

        /* Sent Messages (Right Aligned) */
        .message.sent {
            background: #4CAF50;
            color: white;
            align-self: flex-end;
        }

        /* Chat Input - Stays at Bottom */
        .chat-input {
            display: flex;
            width: 100%;
            padding: 12px;
            border-top: 1px solid #ddd;
            background: #fff;
        }

        .chat-input input {
            flex: 1;
            padding: 8px;
            border: none;
            outline: none;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .chat-input button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            margin-left: 8px;
        }

        /* Message Notifications - Sidebar */
        .message-notifications {
            width: 0; /* Hidden by default */
            overflow: hidden;
            background: #f9f9f9;
            border-left: 1px solid #ddd;
            transition: width 0.3s ease-in-out, padding 0.3s ease-in-out;
            padding: 0;
            height: 100%;
        }

        /* When Open - Expand */
        .message-notifications.open {
            width: 220px;
            padding: 10px;
        }

        /* Sidebar Title */
        .message-notifications h4 {
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        /* Message List */
        .message-notifications ul {
            list-style: none;
            padding: 0;
        }

        /* Message Item */
        .message-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }

        .message-item:hover {
            background: #eee;
        }

        /* Shrink Chat Content when Notifications Open */
        .chat-container.open .chat-content {
            width: calc(100% - 220px); /* Adjust chat width */
        }


        /* Role Toggle Buttons */
        .role-toggle {
            display: flex;
            gap: 5px;
            margin-bottom: 10px;
        }

        .role-btn {
            flex: 1;
            padding: 6px 12px;
            border: 1px solid #4CAF50;
            background: white;
            color: #4CAF50;
            cursor: pointer;
            border-radius: 4px;
            transition: 0.3s;
        }

        .role-btn.active {
            background: #4CAF50;
            color: white;
        }

        /* Timestamp Styling */
        .timestamp {
            font-size: 12px;
            color: #888;
            display: block;
            margin-top: 3px;
        }

        /* Align timestamp below each respective message */
        .received-timestamp {
            text-align: left; /* Timestamp for received messages aligns left */
        }

        .sent-timestamp {
            text-align: right; /* Timestamp for sent messages aligns right */
        }

        .message {
            margin: 10px;
            padding: 10px;
            max-width: 60%;
            border-radius: 10px;
        }
        .sent {
            background-color: #d1ffd6;
            align-self: flex-end;
        }
        .received {
            background-color: #f0f0f0;
            align-self: flex-start;
        }


    </style>

    <title>Student Message</title>
</head>
<body>


    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class="bx bxs-graduation"></i>
            <span class="text">Hi <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
        </a>
        <ul class="side-menu top">
            <li>
                <a href="student-dashboard.php">
                    <i class='bx bxs-dashboard' ></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class='bx bxs-calendar-check' ></i>
                    <span class="text">Attendance</span>
                </a>
            </li>
            <li class="active">
                <a href="student-messages.php">
                    <i class='bx bxs-message-dots' ></i>
                    <span class="text">Message</span>
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
                <a href="logout.php" class="logout">
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
                    <h1>Chat Messages</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="student-messages.php">Home</a>
                        </li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li>
                            <a class="active">Messages</a>
                        </li>
                        
                    </ul>
                </div>
                <a href="#" class="btn-download">
                    <i class='bx bxs-cloud-download' ></i>
                    <span class="text">Download PDF</span>
                </a>
            </div>


            <div class="table-data">
                
                <div class="head">
                    <h3 id="section-title">Recent Messages</h3> 
                    <i class='bx bx-plus'></i>  <!-- Expand -->
                    <i class='bx bxs-chat' id="chatToggle"></i>  <!-- Toggle Button -->
                </div>
                

                <!-- Chat Container (Fixed Layout) -->
                <div class="chat-container">
                    <!-- Chat Content -->
                    <div class="chat-content">
                        <!-- Chat Header -->
                        <div class="chat-header">
                            <h3 id="chatPerson">Select a message</h3>
                        </div>

                        <!-- Chat Box -->
                        <div class="chat-box" id="chatBox">
                            <!-- Will be populated dynamically -->
                        </div>

                        <!-- Chat Input -->
                        <div class="chat-input">
                            <input type="text" id="messageInput" placeholder="Type a message...">
                            <button id="sendMessage"><i class='bx bx-send'></i></button>
                        </div>
                    </div>

                    <!-- Message Notifications Panel -->
                    <div class="message-notifications" id="messageNotifications">
                        <h4>Message Notifications</h4>

                        <!-- HTML Code to Display Data -->
                        <div class="role-toggle">
                            <button class="role-btn active" data-role="client">Clients</button>
                            <button class="role-btn" data-role="supervisor">Supervisor</button>
                        </div>

                        <!-- Client Messages -->
                        <ul class="message-list" data-role="client">
                            <?php foreach ($recent_users as $user): ?>
                                <?php if ($user['role'] === 'Client'): ?>
                                    <li class="message-item" data-username="<?= htmlspecialchars($user['username']) ?>" data-role="client">
                                        <p><strong><?= htmlspecialchars($user['username']) ?></strong></p>
                                        <p>Registered: <span class="registered-time" data-time="<?= htmlspecialchars($user['created_at']) ?>"></span></p>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Supervisor Messages List -->
                        <ul class="message-list" data-role="supervisor" style="list-style: none; padding: 0;">
                            <?php if (!empty($supervisorMessages)): ?>
                                <?php foreach ($supervisorMessages as $msg): ?>
                                    <li class="message-item" data-username="<?= htmlspecialchars($msg['sender']) ?>">
                                        <p><strong><?= htmlspecialchars($msg['sender']) ?>:</strong> <?= htmlspecialchars($msg['message']) ?></p>
                                        <p><small><?= htmlspecialchars($msg['timestamp']) ?></small></p>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>No messages from supervisor.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
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

    <script>
        
        document.addEventListener("DOMContentLoaded", function () {
            // Send Message
            document.getElementById("sendMessage").addEventListener("click", function () {
                const inputField = document.getElementById("messageInput");
                const messageText = inputField.value.trim();
                
                if (messageText !== "") {
                    const chatBox = document.querySelector(".chat-box");
                    const messageDiv = document.createElement("div");
                    messageDiv.classList.add("message", "sent");
                    messageDiv.innerHTML = `<p>${messageText}</p><span class="timestamp">Just now</span>`;
                    chatBox.appendChild(messageDiv);
                    inputField.value = "";
                    chatBox.scrollTop = chatBox.scrollHeight;  // Auto-scroll to the latest message
                }
            });
        });

    </script>

    <script>
        
        

        // Toggle Message Notifications & Adjust Chat Layout
        document.getElementById("chatToggle").addEventListener("click", function() {
            let chatContainer = document.querySelector(".chat-container");
            let messageNotifications = document.getElementById("messageNotifications");

            // Toggle 'open' class to expand/collapse
            messageNotifications.classList.toggle("open");
            chatContainer.classList.toggle("open");
        });

        // Toggle Client/Supervisor Messages
        document.querySelectorAll('.role-btn').forEach(button => {
            button.addEventListener('click', function () {
                document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const role = this.getAttribute('data-role');
                document.querySelectorAll('.message-list').forEach(list => {
                    list.style.display = (list.getAttribute('data-role') === role) ? 'block' : 'none';
                });
            });
        });




        // Attach click listener to message list
        document.addEventListener("click", function (e) {
            if (e.target.closest('.message-item')) {
                const item = e.target.closest('.message-item');
                const username = item.getAttribute('data-username');
                document.getElementById('chatPerson').textContent = username;

                // Load conversation via AJAX
                fetch("load_messages_student.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "chatWith=" + encodeURIComponent(username)
                })
                .then(res => res.json())
                .then(messages => {
                    const chatBox = document.getElementById("chatBox");
                    chatBox.innerHTML = "";

                    messages.forEach(msg => {
                        let msgDiv = document.createElement("div");
                        msgDiv.classList.add("message");

                        if (msg.sender === username) {
                            msgDiv.classList.add("received");
                        } else {
                            msgDiv.classList.add("sent");
                        }

                        msgDiv.innerHTML = `<p>${msg.message}</p><span class="timestamp">${msg.timestamp}</span>`;
                        chatBox.appendChild(msgDiv);
                    });

                    chatBox.scrollTop = chatBox.scrollHeight;
                });
            }
        });

        // Send message
        document.getElementById("sendMessage").addEventListener("click", function() {
            let input = document.getElementById("messageInput");
            let messageText = input.value.trim();
            let receiver = document.getElementById("chatPerson").textContent;

            if (messageText !== "" && receiver !== "Select a message") {
                fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        sender: "supervisor@gmail.com", // or get from PHP session
                        receiver: receiver,
                        message: messageText
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        let chatBox = document.getElementById("chatBox");
                        let newMessage = document.createElement("div");
                        newMessage.classList.add("message", "sent");
                        newMessage.innerHTML = `<p>${messageText}</p><span class="timestamp">Just now</span>`;
                        chatBox.appendChild(newMessage);
                        input.value = "";
                        chatBox.scrollTop = chatBox.scrollHeight;
                    } else {
                        alert("Failed to send message.");
                    }
                });
            }
        });

        fetch('get_messages.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `selected_user_email=${encodeURIComponent(userEmail)}`
        })
        .then(res => res.json())
        .then(messages => {
          // Load messages into the chat box
        });





    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Load Chart.js -->
</body>
</html>