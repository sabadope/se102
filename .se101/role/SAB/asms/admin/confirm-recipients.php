<?php
session_start();

// Debugging: Check if the session is started successfully
if (session_status() === PHP_SESSION_ACTIVE) {
    // Debugging: Output the session status
    var_dump("Session started successfully!");
} else {
    // Debugging: Output an error message if the session failed to start
    var_dump("Session failed to start!");
}

// error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the selected recipients' data from the session
        $selectedRecipients = $_SESSION['selectedRecipients'];

        // Debugging: Output the selected recipients' data
        var_dump("Selected Recipients Data: ", $selectedRecipients);

        // Call the function to get student details based on selected IDs
        $studentDetails = getStudentDetails($selectedRecipients['selectedStudents'], $dbh);

        // Debugging: Output the obtained student details
        var_dump("Obtained Student Details: ", $studentDetails);

        // Add your logic to send messages or perform other actions with the obtained student details

        // Clear the session data to avoid conflicts on subsequent requests
        unset($_SESSION['selectedRecipients']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SRMS Admin Select Recipients</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
    <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.min.css"/>
    <link rel="stylesheet" href="css/main.css" media="screen">
    <script src="js/modernizr/modernizr.min.js"></script>
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body class="top-navbar-fixed">
<div class="main-wrapper">
    <!-- ========== TOP NAVBAR ========== -->
    <?php include('includes/topbar.php'); ?>
    <!-- ========== WRAPPER FOR BOTH SIDEBARS & MAIN CONTENT ========== -->
    <div class="content-wrapper">
        <div class="content-container">
            <?php include('includes/leftbar.php'); ?>
            <div class="main-page">
                <div class="container-fluid">
                    <div class="row page-title-div">
                        <div class="col-md-6">
                            <h2 class="title">Add Recipients</h2>
                        </div>
                    </div>
                    <div class="row breadcrumb-div">
                        <div class="col-md-6">
                            <ul class="breadcrumb">
                                <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                <li> Students</li>
                                <li class="active">Confirm Recipients</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php
                if (isset($_SESSION['selectedRecipients'])) {
                    var_dump($_SESSION['selectedRecipients']); // Add this line for debugging
                }
                ?>
<!-- Confirmation Section -->
<section class="section">
    <div class="container-fluid">
        <!-- Display selected recipients data -->
        <div class="confirmation-section">
            <h4>Selected Recipients</h4>
            <!-- Debugging: Display selectedRecipients session data -->
            <?php
            if (isset($_SESSION['selectedRecipients'])) {
                echo '<pre>';
                print_r($_SESSION['selectedRecipients']);
                echo '</pre>';
            }
            ?>

            <!-- Adjust the HTML structure based on your selected recipients data -->
            <?php
            // Example: Displaying specific students
            if (!empty($_SESSION['selectedRecipients'])) {
                echo '<p>Selected Students:</p>';
                foreach ($_SESSION['selectedRecipients'] as $studentId) {
                    // Debugging: Display the current student ID
                    echo '<p>Current Student ID: ' . $studentId . '</p>';

                    // Fetch and display student information based on $studentId
                    // Adjust this part based on your data structure
                    echo '<p>Student: ' . $studentDetails[0]['StudentName'] . '</p>';
                }
            }
            ?>
        </div>

        <!-- Confirmation Actions -->
        <div class="confirmation-actions">
            <!-- Add any confirmation messages or buttons for proceeding with sending the message -->
            <button id="proceedSendBtn" class="btn btn-primary">Proceed to Send</button>
            <!-- Add any other buttons or actions as needed -->
        </div>
    </div>
</section>

            </div>
        </div>
    </div>
</div>

<!-- ========== COMMON JS FILES ========== -->
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/pace/pace.min.js"></script>
<script src="js/lobipanel/lobipanel.min.js"></script>
<script src="js/iscroll/iscroll.js"></script>

<!-- ========== PAGE JS FILES ========== -->
<script src="js/prism/prism.js"></script>
<script src="js/DataTables/datatables.min.js"></script>

<!-- ========== THEME JS ========== -->
<script src="js/main.js"></script>
<script>
    $(function ($) {
        $('#example').DataTable();
    });
</script>

<script>
    // JavaScript/jQuery for handling user interactions on this page
    $(document).ready(function () {
        $('#proceedSendBtn').click(function () {
            // Additional actions or redirection logic
            // window.location.href = 'send-message.php';
        });
    });
</script>
</body>
</html>

<?php
// Clear the selected recipients session data after displaying it
unset($_SESSION['selectedRecipients']);
?>
