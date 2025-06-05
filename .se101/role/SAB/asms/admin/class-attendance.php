<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['class_id'])) {
    header("Location: dashboard.php");
    exit;
}

$classSql = "SELECT * FROM tblclasses WHERE id = :classId";
$classQuery = $dbh->prepare($classSql);
$classQuery->bindParam(':classId', $_GET['class_id'], PDO::PARAM_INT);
$classQuery->execute();
$classInfo = $classQuery->fetch(PDO::FETCH_OBJ);

if (!$classInfo) {
    echo "Class not found!";
    exit;
}

$studentSql = "SELECT * FROM tblstudents WHERE ClassId = :classId";
$studentQuery = $dbh->prepare($studentSql);
$studentQuery->bindParam(':classId', $_GET['class_id'], PDO::PARAM_INT);
$studentQuery->execute();
$students = $studentQuery->fetchAll(PDO::FETCH_OBJ);

if (!$students) {
    echo "No students found for this class!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attendanceDate = $_POST['attendance_date'];
    $attendanceData = $_POST['attendance'];

    if (empty($attendanceDate)) {
        $error = "Please select an attendance date.";
    } else {
        if (updateAttendance($classInfo->id, $attendanceDate, $attendanceData)) {
            $msg = "Attendance submitted successfully!";
        } else {
            $error = "Error submitting attendance. Please try again.";
        }
    }
}

// Function to update attendance records in the database
function updateAttendance($classId, $attendanceDate, $attendanceData)
{
    global $dbh;

    try {
        // Start a transaction
        $dbh->beginTransaction();

        // Example: Insert attendance data into a hypothetical tblattendance table
        $insertSql = "INSERT INTO tblattendance (ClassId, AttendanceDate, RollId) VALUES (:classId, :attendanceDate, :rollId)";
        $insertQuery = $dbh->prepare($insertSql);

        // Loop through each selected student's RollId and insert into tblattendance
        foreach ($attendanceData as $rollId) {
            // Bind parameters inside the loop
            $insertQuery->bindParam(':classId', $classId, PDO::PARAM_INT);
            $insertQuery->bindParam(':attendanceDate', $attendanceDate, PDO::PARAM_STR);
            $insertQuery->bindParam(':rollId', $rollId, PDO::PARAM_INT);

            // Execute the query for each student
            $insertQuery->execute();
        }

        // Commit the transaction
        $dbh->commit();

        // Return true if the update is successful
        return true;
    } catch (PDOException $e) {
        // An error occurred, rollback the transaction
        $dbh->rollBack();

        // Print the error message for debugging (you can remove this in a production environment)
        echo "Error: " . $e->getMessage();

        // Return false to indicate failure
        return false;
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SRMS Admin Manage Students</title>
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
        .class-card {
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            padding: 15px;
            text-align: center;
        }
    </style>
</head>
<body class="top-navbar-fixed">
    <div class="main-wrapper">
        <!-- ========== TOP NAVBAR ========== -->
        <?php include('includes/topbar.php');?>
        <!-- ========== WRAPPER FOR BOTH SIDEBARS & MAIN CONTENT ========== -->
        <div class="content-wrapper">
            <div class="content-container">
                <?php include('includes/leftbar.php');?>
                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row page-title-div">
                            <div class="col-md-6">
                                <h2 class="title">Attendance Overview</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li> Students</li>
                                    <li class="active">Attendance</li>
                                    <li><?php echo htmlentities($classInfo->ClassName . ' (' . $classInfo->Section . ')'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h5>Class Attendance - <?php echo htmlentities($classInfo->ClassName . ' (' . $classInfo->Section . ')'); ?></h5>
                                            </div>
                                        </div>
                                        <?php if ($msg) { ?>
                                            <div class="alert alert-success left-icon-alert" role="alert">
                                                <strong>Well done!</strong><?php echo htmlentities($msg); ?>
                                            </div>
                                        <?php } else if ($error) { ?>
                                            <div class="alert alert-danger left-icon-alert" role="alert">
                                                <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                            </div>
                                        <?php } ?>
                                        <div class="panel-body p-20">
                                            <form method="post">
                                                <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Roll ID</th>
                                                            <th>Student Name</th>
                                                            <th>Present</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Roll ID</th>
                                                            <th>Student Name</th>
                                                            <th>Present</th>
                                                        </tr>
                                                    </tfoot>
                                                    <tbody>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td>Select All</td>
                                                            <td><input type="checkbox" id="selectAll"></td>
                                                        </tr>
                                                        <?php foreach ($students as $key => $student): ?>
                                                            <tr>
                                                                <td><?php echo $key + 1; ?></td>
                                                                <td><?php echo htmlentities($student->RollId); ?></td>
                                                                <td><?php echo htmlentities($student->StudentName); ?></td>
                                                                <td><input type="checkbox" name="attendance[]" value="<?php echo htmlentities($student->RollId); ?>"></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <div class="form-group">
                                                    <label for="datepicker">Select Date:</label>
                                                    <input type="date" class="form-control" id="datepicker" name="attendance_date" value="<?= date('Y-m-d'); ?>" required>
                                                </div>
                                                <button class="btn primary" type="submit" name="submit">Submit Attendance</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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

            $('#example2').DataTable({
                "scrollY": "300px",
                "scrollCollapse": true,
                "paging": false
            });

            $('#example3').DataTable();
        });

        // Implement "Select All" functionality
        document.getElementById('selectAll').addEventListener('change', function () {
            var checkboxes = document.querySelectorAll('input[name^="attendance"]');
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = document.getElementById('selectAll').checked;
            });
        });

        // Initialize the datepicker
        $(document).ready(function () {
            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true
            });
        });
    </script>
</body>
</html>
