<?php
    require_once '../src/config.php'; // Include DB connection

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/messages.css">

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


        /* Chat Container - Layout Holder */
        .chat-container {
            display: flex;
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            position: relative;
            transition: width 0.3s ease-in-out;
            justify-content: center;
            align-items: center; /* Centers vertically */
        }

        /* Optional: make iframe look better on smaller screens */
        .chat-container iframe {
            width: 100%;
            border: none; /* Remove any border */
            display: block; /* Ensures it behaves like a block-level element */
        }

        #content main .table-data {
            display: flex;
            flex-wrap: wrap;
            grid-gap: 0px;
            margin-top: 24px;
            width: 100%;
            height: 100%;
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
    </style>

    <title>Client Message</title>
</head>
<body>


    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-user'></i>
            <span class="text">Hi <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
        </a>
        <ul class="side-menu top">
            <li>
                <a href="client-dashboard.php">
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
            <li class="active">
                <a href="client-messages.php">
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
                <a href="client-logout.php" class="logout">
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
                            <a href="client-messages.php">Home</a>
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
                

                <!-- Chat Container -->
                <div class="chat-container">

                    <iframe id="chatIframe" src="index.php" width="100%" frameborder="0"></iframe>

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
        
        // Send Message Event
        document.getElementById('sendMessage').addEventListener('click', function () {
            const message = document.getElementById('messageInput').value;
            const receiverUsername = document.getElementById('chatPerson').textContent; // name of the selected user

            if (message.trim() === '') return;

            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `receiver=${encodeURIComponent(receiverUsername)}&message=${encodeURIComponent(message)}`
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('messageInput').value = '';
                loadMessages(receiverUsername); // reload chat after sending
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

        // Call updateTimes on page load to initialize
        updateTimes();

        // Role toggle functionality
        document.querySelectorAll('.role-btn').forEach(button => {
            button.addEventListener('click', function() {
                const role = this.getAttribute('data-role');
                
                // Show the appropriate message list based on the clicked role
                document.querySelectorAll('.message-list').forEach(list => {
                    list.style.display = (list.getAttribute('data-role') === role) ? 'block' : 'none';
                });

                // Toggle active class on buttons
                document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        });

        document.querySelectorAll('.message-item').forEach(item => {
            item.addEventListener('click', function() {
                const username = this.getAttribute('data-username');
                document.getElementById('chatPerson').textContent = username;

                fetch("load_messages.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "chatWith=" + encodeURIComponent(username)
                })
                .then(res => res.json())
                .then(messages => {
                    const chatBox = document.getElementById("chatBox");
                    chatBox.innerHTML = ""; // Clear existing

                    messages.forEach(msg => {
                        let msgDiv = document.createElement("div");
                        msgDiv.classList.add("message");

                        if (msg.sender_name === username) {
                            msgDiv.classList.add("received");
                        } else {
                            msgDiv.classList.add("sent");
                        }

                        msgDiv.innerHTML = `<p>${msg.message}</p><span class="timestamp">${msg.timestamp}</span>`;
                        chatBox.appendChild(msgDiv);
                    });

                    chatBox.scrollTop = chatBox.scrollHeight;
                });
            });
        });


        console.log("Fetching chat with:", username);


    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Load Chart.js -->
</body>
</html>