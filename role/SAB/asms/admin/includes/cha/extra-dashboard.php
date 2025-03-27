<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
} else {

    $sqlMaleCount = "SELECT COUNT(*) as maleCount FROM tblstudents WHERE Gender = 'Male'";
    $queryMaleCount = $dbh->prepare($sqlMaleCount);
    $queryMaleCount->execute();
    $resultMaleCount = $queryMaleCount->fetch(PDO::FETCH_ASSOC);
    $maleCount = $resultMaleCount['maleCount'];

    $sqlFemaleCount = "SELECT COUNT(*) as femaleCount FROM tblstudents WHERE Gender = 'Female'";
    $queryFemaleCount = $dbh->prepare($sqlFemaleCount);
    $queryFemaleCount->execute();
    $resultFemaleCount = $queryFemaleCount->fetch(PDO::FETCH_ASSOC);
    $femaleCount = $resultFemaleCount['femaleCount'];


    //TIMETABLE
    $currentDayOfWeek = date('l');

    $sql = "SELECT tt.id, CONCAT(cl.ClassName, ' (', cl.Section, ')') AS ClassName, tt.StartTime, tt.EndTime, sb.SubjectName, t.Name AS TeacherName
            FROM tbltimetable tt
            LEFT JOIN tblclasses cl ON tt.ClassId = cl.id
            LEFT JOIN tblsubjects sb ON tt.SubjectId = sb.id
            LEFT JOIN tblteachers t ON tt.TeacherTNumber = t.TNumber
            WHERE tt.Day = :currentDayOfWeek
            ORDER BY tt.StartTime"; // Modify the query to select classes for the current day and order by start time

    $query = $dbh->prepare($sql);
    $query->bindParam(':currentDayOfWeek', $currentDayOfWeek, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);


    //FOR ATTENDANCE

        // Fetch active school period for the current date
    $currentDate = date('Y-m-d');
    $sql = "SELECT * FROM tblschoolperiods WHERE StartDate <= :currentDate AND EndDate >= :currentDate";
    $query = $dbh->prepare($sql);
    $query->bindParam(':currentDate', $currentDate, PDO::PARAM_STR);
    $query->execute();
    $periods = $query->fetchAll(PDO::FETCH_ASSOC);

    if (empty($periods)) {
        $error = "The attendance records for today are not available as they do not fall within any defined school period.";
    } else {
        $schoolPeriod = $periods[0];
        $startDate = $schoolPeriod['StartDate'];
        $endDate = $schoolPeriod['EndDate'];

        // Fetch all students and their classes
        $sqlStudents = "SELECT tblstudents.StudentName, tblstudents.RollId, tblstudents.ClassId, tblclasses.ClassName, tblclasses.Section
                        FROM tblstudents
                        JOIN tblclasses ON tblstudents.ClassId = tblclasses.id";
        $queryStudents = $dbh->prepare($sqlStudents);
        $queryStudents->execute();
        $students = $queryStudents->fetchAll(PDO::FETCH_ASSOC);

        // Attendance date selection
        $attendanceDate = isset($_POST['attendance_date']) ? $_POST['attendance_date'] : $currentDate;

        // Query to get latest attendance for selected date
        $sqlAttendance = "SELECT a.RollId, a.IsPresent, s.ClassId
                          FROM tblattendance a
                          JOIN (SELECT RollId, MAX(timestamp) AS MaxTimestamp
                                FROM tblattendance
                                WHERE AttendanceDate = :attendanceDate
                                GROUP BY RollId) latest_attendance ON a.RollId = latest_attendance.RollId AND a.timestamp = latest_attendance.MaxTimestamp
                          JOIN tblstudents s ON a.RollId = s.RollId";
        $queryAttendance = $dbh->prepare($sqlAttendance);
        $queryAttendance->bindParam(':attendanceDate', $attendanceDate, PDO::PARAM_STR);
        $queryAttendance->execute();
        $existingAttendance = $queryAttendance->fetchAll(PDO::FETCH_ASSOC);

        // Calculate attendance statistics
        $attendanceStatus = [];
        foreach ($existingAttendance as $record) {
            $attendanceStatus[$record['RollId']] = $record['IsPresent'];
        }

        // Initialize variables for overall summary
        $totalStudents = count($students);
        $presentCount = 0;
        $absentCount = 0;
        $notSubmittedCount = 0;

        // Calculate individual attendance counts
        foreach ($students as $student) {
            $rollId = $student['RollId'];
            if (isset($attendanceStatus[$rollId])) {
                if ((int)$attendanceStatus[$rollId] === 1) {
                    $presentCount++;
                } elseif ((int)$attendanceStatus[$rollId] === 0) {
                    $absentCount++;
                }
            } else {
                $notSubmittedCount++;
            }
        }

        // Overall attendance percentage
        $attendancePercentage = ($totalStudents > 0) ? ($presentCount / $totalStudents) * 100 : 0;

    }
}

?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SRMS System | Dashboard</title>
        <!-- ========== CSS ========== -->
        <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
        <link rel="stylesheet" href="css/fontawesome/css/all.min.css" media="screen">
        <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
        <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
        <link rel="stylesheet" href="css/toastr/toastr.min.css" media="screen">
        <link rel="stylesheet" href="css/icheck/skins/line/blue.css">
        <link rel="stylesheet" href="css/icheck/skins/line/red.css">
        <link rel="stylesheet" href="css/icheck/skins/line/green.css">
        <link rel="stylesheet" href="css/main.css" media="screen">
        <!-- ========== JS ========== -->
        <script src="js/modernizr/modernizr.min.js"></script>

        <style>
            body{
                background-color: #f4dcd2;
            }
            /* Switch between tables and cards */
            @media (max-width: 768px) {
                .dashboard-table {
                    display: none;
                }
                .dashboard-card {
                    display: block;
                    margin-bottom: -24px;
                }
                .dashboard-card-top {
                    padding: 0 20px;
                }
            }

            @media (min-width: 769px) {
                .dashboard-table {
                    display: block;
                    margin-bottom: -24px;
                }
                .dashboard-card {
                    display: none;
                }
            }
        </style>
    </head>

    <body class="top-navbar-fixed">
<div class="main-wrapper">
    <?php include('includes/topbar.php'); ?>
    <div class="content-wrapper">
        <div class="content-container">
            <?php include('includes/leftbar.php'); ?>
            <div class="main-page">
                <div class="container-fluid">
                    <div class="row page-title-div">
                        <div class="col-sm-6">
                            <h2 class="title">Dashboard</h2>
                        </div>
                        <!-- /.col-sm-6 -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->

                <section class="section">
                    <div class="container">
                        <div class="row dashboard-card-top">

                            <!-- Card 1: Registered Users -->
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 mb-4 mx-auto">
                                <a class="dashboard-stat bg-primary" href="manage-students.php">
                                    <?php
                                    $sql1 = "SELECT StudentId from tblstudents ";
                                    $query1 = $dbh->prepare($sql1);
                                    $query1->execute();
                                    $totalstudents = $query1->rowCount();
                                    ?>
                                    <span class="number counter"><?php echo htmlentities($totalstudents); ?></span>
                                    <span class="name">Regd Students</span>
                                    <span class="bg-icon"><i class="fa fa-users"></i></span>
                                </a>
                                <!-- /.dashboard-stat -->
                            </div>

                            <!-- Card 2: Subjects Listed -->
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 mb-4 mx-auto">
                                <a class="dashboard-stat bg-danger" href="manage-subjects.php">
                                    <?php
                                    $sql = "SELECT id from tblsubjects ";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $totalsubjects = $query->rowCount();
                                    ?>
                                    <span class="number counter"><?php echo htmlentities($totalsubjects); ?></span>
                                    <span class="name">Subjects Listed</span>
                                    <span class="bg-icon"><i class="fa fa-ticket"></i></span>
                                </a>
                                <!-- /.dashboard-stat -->
                            </div>

                            <!-- Card 3: Total Classes Listed -->
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 mb-4 mx-auto">
                                <a class="dashboard-stat bg-warning" href="manage-classes.php">
                                    <?php
                                    $sql2 = "SELECT id from tblclasses ";
                                    $query2 = $dbh->prepare($sql2);
                                    $query2->execute();
                                    $totalclasses = $query2->rowCount();
                                    ?>
                                    <span class="number counter"><?php echo htmlentities($totalclasses); ?></span>
                                    <span class="name">Classes</span>
                                    <span class="bg-icon"><i class="fa fa-bank"></i></span>
                                </a>
                                <!-- /.dashboard-stat -->
                            </div>

                            <!-- Card 4: Total Teachers -->
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 mb-4 mx-auto">
                                <a class="dashboard-stat bg-info" href="manage-teachers.php">
                                    <?php
                                    $sqlTeachers = "SELECT id from tblteachers";
                                    $queryTeachers = $dbh->prepare($sqlTeachers);
                                    $queryTeachers->execute();
                                    $totalTeachers = $queryTeachers->rowCount();
                                    ?>
                                    <span class="number counter"><?php echo htmlentities($totalTeachers); ?></span>
                                    <span class="name">Teachers</span>
                                    <span class="bg-icon"><i class="fa fa-user"></i></span>
                                </a>
                                <!-- /.dashboard-stat -->
                            </div>

                            <!-- Card 5: Inbox -->
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 mb-4 mx-auto">
                                <a class="dashboard-stat bg-success" href="manage-teachers.php">
                                    <span class="number counter">0</span>
                                    <span class="name">Inbox</span>
                                    <span class="bg-icon"><i class="fa fa-inbox"></i></span>
                                </a>
                                <!-- /.dashboard-stat -->
                            </div>

                            <!-- Card 6: Total Trophies -->
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 mb-4 mx-auto">
                                <a class="dashboard-stat bg-warning" href="manage-teachers.php">
                                    <span class="number counter">0</span>
                                    <span class="name">Trophies</span>
                                    <span class="bg-icon"><i class="fa fa-trophy"></i></span>
                                </a>
                                <!-- /.dashboard-stat -->
                            </div>
                        </div>
                        <!-- /.row -->

                    <!-- Main Row -->
                        <div class="row">
                            <!-- Column for Chart and Upcoming Events -->
                            <div class="col-lg-8 col-md-12 mb-4">
                                <div class="row">

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="card shadow text-center" style="background-color: #ffffff;">
                                            <?php if (empty($schoolPeriod)) { ?>
                                            <div class="alert alert-danger left-icon-alert" role="alert" style="margin-top: 32px;">
                                                <strong>Error: Attendance Records Unavailable.</strong> <?php echo htmlentities($error); ?><br>
                                                <a href="manage-school-period.php"><strong>Manage School Periods</strong></a><br>
                                                <a href="view-attendance.php"><strong>Search Attendance</strong></a>
                                            </div>
                                            <?php } else { ?>
                                            <h4>Attendance Summary for <?php echo htmlentities(date('l, d-M-Y', strtotime($attendanceDate))); ?></h4>
                                            <div class="row" style="padding: 0 20px;">
                                                <!-- Card: Present Students-->
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mb-4">
                                                    <a class="dashboard-stat bg-success" href="view-attendance.php">
                                                        <span class="number counter"><?php echo htmlentities($presentCount); ?></span>
                                                        <span class="name">Present</span>
                                                        <span class="bg-icon"><i class="fa fa-check-circle"></i></span>
                                                    </a>
                                                </div>
                                                <!-- Card: Absent Students-->
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mb-4">
                                                    <a class="dashboard-stat bg-danger" href="view-attendance.php">
                                                        <span class="number counter"><?php echo htmlentities($absentCount); ?></span>
                                                        <span class="name">Absent</span>
                                                        <span class="bg-icon"><i class="fa fa-times-circle"></i></span>
                                                    </a>
                                                </div>
                                                <!-- Card: Not Marked-->
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mb-4">
                                                    <a class="dashboard-stat bg-warning" href="view-attendance.php">
                                                        <span class="number counter"><?php echo htmlentities($notSubmittedCount); ?></span>
                                                        <span class="name">Not Marked</span>
                                                        <span class="bg-icon"><i class="fa fa-question-circle"></i></span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row p-20">
                                                <!-- Attendance Percentage Gauge -->
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center">
                                                    <div id="attendance-percentage-gauge"></div>
                                                </div>
                                                <!-- Attendance Marked Gauge -->
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center">
                                                    <div id="attendance-marked-gauge"></div>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    
                                    <!--div class="col-lg-12 mb-4">
                                        <?php if (!empty($schoolPeriod)) { ?>
                                            <div class="class-summary-card">
                                                <h4>Attendance Summary for <?php echo htmlentities($attendanceDate); ?></h4>
                                                <p class="summary">
                                                    Total Students: <?php echo htmlentities($totalStudents); ?><br>
                                                    Present Students: <?php echo htmlentities($presentCount); ?><br>
                                                    Absent Students: <?php echo htmlentities($absentCount); ?><br>
                                                    Attendance Not Submitted: <?php echo htmlentities($notSubmittedCount); ?><br>
                                                </p>
                                            </div>
                                        <?php } else { ?>
                                            <div class="alert alert-danger left-icon-alert" role="alert">
                                                <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                            </div>
                                        <?php } ?>
                                    </div>-->




                                    <!-- Card: Upcoming Events -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dashboard-table">
                                        <div class="card shadow text-center" style="background-color: #ffffff;">
                                            <div class="card-body">
                                                <h5 class="card-title">Upcoming Events</h5>
                                                <!-- Display Upcoming Events from Database -->
                                                <table class="table table-bordered text-left" style="background-color: #f8f9fa;">
                                                    <thead>
                                                        <tr>
                                                            <th>Activity Name</th>
                                                            <th>Date</th>
                                                            <th>Time</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th>Activity Name</th>
                                                            <th>Date</th>
                                                            <th>Time</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </tfoot>
                                                    <tbody>
                                                        <?php
                                                        // Fetch data from tblupcomingevents table
                                                        $sqlUpcomingEvents = "SELECT * FROM tblupcomingevents WHERE EventDate >= CURDATE() ORDER BY EventDate, EventTime LIMIT 5";
                                                        $queryUpcomingEvents = $dbh->prepare($sqlUpcomingEvents);
                                                        $queryUpcomingEvents->execute();
                                                        $upcomingEvents = $queryUpcomingEvents->fetchAll(PDO::FETCH_ASSOC);

                                                        if (empty($upcomingEvents)) {
                                                            echo '<tr><td colspan="4" class="text-center">No upcoming events. Click <a href="add-event.php"><strong>HERE</strong></a> to add an event.</td></tr>';
                                                        } else {
                                                            foreach ($upcomingEvents as $event) {
                                                                // Determine the display value for the date
                                                                $eventDate = date('Y-m-d', strtotime($event['EventDate']));
                                                                $formattedDate = '';

                                                                if ($eventDate === date('Y-m-d')) {
                                                                    $formattedDate = 'Today';
                                                                } elseif ($eventDate === date('Y-m-d', strtotime('tomorrow'))) {
                                                                    $formattedDate = 'Tomorrow';
                                                                } else {
                                                                    $formattedDate = date('d-M-Y', strtotime($event['EventDate']));
                                                                }

                                                                echo "<tr>";
                                                                echo "<td>{$event['ActivityName']}</td>";
                                                                echo "<td>{$formattedDate}</td>";
                                                                echo "<td>{$event['EventTime']}</td>";
                                                                echo "<td><a href='edit-event.php?id={$event['id']}'>View/Edit</a></td>";
                                                                echo "</tr>";
                                                            }
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-left" style="margin-left: 10px;">Click <strong><a href="add-event.php">Here</a></strong> to add an event</p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Upcoming Events -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dashboard-card">
                                        <div class="card shadow text-center" style="background-color: #ffffff;">
                                            <div class="card-body">
                                                <h5 class="card-title">Upcoming Events</h5>
                                                <div class="row" style="padding: 0 20px">
                                                    <!-- Display Upcoming Events from Database -->
                                                    <?php
                                                    // Fetch data from tblupcomingevents table
                                                    $sqlUpcomingEvents = "SELECT * FROM tblupcomingevents WHERE EventDate >= CURDATE() ORDER BY EventDate, EventTime LIMIT 5";
                                                    $queryUpcomingEvents = $dbh->prepare($sqlUpcomingEvents);
                                                    $queryUpcomingEvents->execute();
                                                    $upcomingEvents = $queryUpcomingEvents->fetchAll(PDO::FETCH_ASSOC);

                                                    if (empty($upcomingEvents)) {
                                                        echo '<div class="col-12"><div class="alert alert-warning">No upcoming events. Click <a href="add-event.php"><strong>HERE</strong></a> to add an event.</div></div>';
                                                    } else {
                                                        foreach ($upcomingEvents as $event) {
                                                            // Determine the display value for the date
                                                            $eventDate = date('Y-m-d', strtotime($event['EventDate']));
                                                            $formattedDate = '';

                                                            if ($eventDate === date('Y-m-d')) {
                                                                $formattedDate = 'Today';
                                                            } elseif ($eventDate === date('Y-m-d', strtotime('tomorrow'))) {
                                                                $formattedDate = 'Tomorrow';
                                                            } else {
                                                                $formattedDate = date('d-M-Y', strtotime($event['EventDate']));
                                                            }

                                                            echo '<div class="col-lg-3 col-sm-6 col-xs-12 mb-4">';
                                                            echo '<div class="alert alert-info text-left">';
                                                            echo '<strong>' . htmlentities($event['ActivityName']) . '</strong><br>';
                                                            echo '<span class="text-muted">Date: ' . htmlentities($formattedDate) . '</span><br>';
                                                            echo '<strong>Time:</strong> ' . htmlentities($event['EventTime']) . '<br>';
                                                            echo '<a href="edit-event.php?id=' . htmlentities($event['id']) . '">View/Edit</a>';
                                                            echo '</div></div>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <p class="text-left" style="margin-left:10px">Click <strong><a href="add-event.php">Here</a></strong> to add an event</p>
                                            </div>
                                        </div>
                                    </div>



                                    <!-- Time table -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dashboard-table">
                                        <div class="card shadow text-center" style="background-color: #ffffff;">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo 'Time Table For Today (' . date('l') . ')'; ?></h5>
                                                <!-- Display Timetable Entries from Database -->
                                                <table id="example" class="display table table-striped table-bordered text-left" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Class</th>
                                                            <th>Start Time</th>
                                                            <th>End Time</th>
                                                            <th>Subject</th>
                                                            <th>Teacher</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Class</th>
                                                            <th>Start Time</th>
                                                            <th>End Time</th>
                                                            <th>Subject</th>
                                                            <th>Teacher</th>
                                                        </tr>
                                                    </tfoot>
                                                    <tbody>
                                                        <?php
                                                        $cnt = 1;
                                                        foreach ($results as $result) { ?>
                                                            <tr>
                                                                <td><?php echo htmlentities($cnt); ?></td>
                                                                <td><?php echo htmlentities($result->ClassName); ?></td>
                                                                <td><?php echo htmlentities($result->StartTime); ?></td>
                                                                <td><?php echo htmlentities($result->EndTime); ?></td>
                                                                <td><?php echo htmlentities($result->SubjectName); ?></td>
                                                                <td><?php echo htmlentities($result->TeacherName); ?></td>
                                                            </tr>
                                                            <?php $cnt++;
                                                        } ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-left" style="margin-left:10px">Click <strong><a href="manage-timetable.php">Here</a></strong> manage or view the entire timetable</p>
                                            </div>
                                        </div>
                                           
                                    </div>
                                    <!-- End Time Table -->

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dashboard-card">
                                        <div class="card shadow text-center" style="background-color: #ffffff;">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo 'Time Table For Today (' . date('l') . ')'; ?></h5>
                                                <div class="row" style="padding: 0 20px">
                                                    <!-- Display Upcoming Events from Database -->
                                                    <?php
                                                    usort($results, function($a, $b) {
                                                        return strtotime($a->StartTime) - strtotime($b->StartTime);
                                                    });

                                                    $cnt = 1;
                                                    foreach ($results as $result) { ?>
                                                        <div class="col-lg-3 col-sm-3 col-xs-12 mb-4">
                                                            <div class="alert alert-info text-left">
                                                                <strong><?php echo htmlentities($result->ClassName); ?></strong><br>
                                                                <span class="text-muted"><?php echo htmlentities($result->SubjectName); ?></span><br>
                                                                <strong>Start Time:</strong> <?php echo htmlentities($result->StartTime); ?><br>
                                                                <strong>End Time:</strong> <?php echo htmlentities($result->EndTime); ?><br>
                                                                <strong>Teacher:</strong> <?php echo htmlentities($result->TeacherName); ?><br>
                                                            </div>
                                                        </div>
                                                    <?php $cnt++;
                                                    } ?>
                                                </div>
                                                <p class="text-left" style="margin-left:10px">Click <strong><a href="manage-timetable.php">Here</a></strong> to manage or view the entire timetable</p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- End row -->
                            </div>
                            <!-- End col-lg-8 -->
                            
                            <div class="col-lg-4 col-md-12 mb-4">

                                <div class="row">
                                    <div class="col-lg-12 col-xs-6">
                                        <!-- Pie Chart: Gender Distribution -->
                                        <div class="card" style="background-color:#ffffff">
                                            <div class="card-body">
                                                <h5 class="card-title" style="margin-left: 20px">Students <?php echo htmlentities($totalstudents); ?></h5>
                                                <!-- Pie Chart -->
                                                <div id="pieChart"></div>

                                                <script>
                                                    document.addEventListener("DOMContentLoaded", () => {
                                                        new ApexCharts(document.querySelector("#pieChart"), {
                                                            series: [<?php echo $maleCount; ?>, <?php echo $femaleCount; ?>],
                                                            chart: {
                                                                height: 350,
                                                                type: 'pie',
                                                                toolbar: {
                                                                    show: true
                                                                }
                                                            },
                                                            labels: ['Male', 'Female'],
                                                            responsive: [{
                                                                breakpoint: 480,
                                                                options: {
                                                                    chart: {
                                                                        width: 200
                                                                    },
                                                                    legend: {
                                                                        position: 'bottom'
                                                                    }
                                                                }
                                                            }]
                                                        }).render();
                                                    });
                                                </script>
                                                <!-- End Pie Chart -->

                                            </div>
                                        </div>
                                        <!-- End Gender Distribution -->
                                    </div>

                                    <div class="col-lg-12 col-xs-6">
                                        <!-- Average Results -->
                                        <div class="card text-center rounded shadow" style="background-color: #ffffff;">
                                            <div class="card-body">
                                                <?php
                                                // Retrieve the average results for the latest exam period
                                                $sqlAvgResults = "SELECT AVG(marks) as avgMarks FROM tblresult WHERE ExamPeriodId = (SELECT id FROM tblexamperiod ORDER BY StartDate DESC LIMIT 1)";
                                                $queryAvgResults = $dbh->prepare($sqlAvgResults);
                                                $queryAvgResults->execute();
                                                $resultAvgResults = $queryAvgResults->fetch(PDO::FETCH_ASSOC);
                                                $averageResults = round($resultAvgResults['avgMarks'], 2);

                                                // Retrieve the name of the latest exam period
                                                $sqlLatestExamPeriod = "SELECT PeriodName FROM tblexamperiod ORDER BY StartDate DESC LIMIT 1";
                                                $queryLatestExamPeriod = $dbh->prepare($sqlLatestExamPeriod);
                                                $queryLatestExamPeriod->execute();
                                                $resultLatestExamPeriod = $queryLatestExamPeriod->fetch(PDO::FETCH_ASSOC);
                                                $latestExamPeriodName = $resultLatestExamPeriod['PeriodName'];
                                                ?>
                                                <h5 class="card-title"></h5>
                                                <div id="average-results-gauge"></div>
                                            </div>
                                        </div>
                                        <!-- End Average Results -->
                                    </div>
                                </div>


                                <!-- Class Averages -->
                                <div class="card text-center shadow" style="background-color: #ffffff">
                                    <div class="card-body">
                                        <?php
                                            // Fetch average results for individual classes
                                            $sqlAvgResultsByClass = "SELECT CONCAT(c.ClassName, ' (', c.Section, ')') AS ClassNameSection, AVG(r.marks) as avgMarks
                                                                      FROM tblresult r
                                                                      INNER JOIN tblstudents s ON r.RollId = s.RollId
                                                                      INNER JOIN tblclasses c ON s.ClassId = c.id
                                                                      WHERE r.ExamPeriodId = (SELECT id FROM tblexamperiod ORDER BY StartDate DESC LIMIT 1)
                                                                      GROUP BY c.id";

                                            $queryAvgResultsByClass = $dbh->prepare($sqlAvgResultsByClass);
                                            $queryAvgResultsByClass->execute();
                                            $resultAvgResultsByClass = $queryAvgResultsByClass->fetchAll(PDO::FETCH_ASSOC);
                                        ?>
                                        <h5 class="card-title">Class Averages</h5>
                                        <ul class="list-group">
                                            <?php foreach ($resultAvgResultsByClass as $classData): ?>
                                                <li class="list-group-item">
                                                    <?php echo $classData['ClassNameSection']; ?>: <?php echo round($classData['avgMarks'], 2); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                                <!-- End class Averages -->
                            </div>
                            <!-- End col=lg-4 -->
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<div class="foot">
    <footer></footer>
</div>



        <!-- ========== COMMON JS FILES ========== -->
        <script src="js/jquery/jquery-2.2.4.min.js"></script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
        <script src="js/pace/pace.min.js"></script>
        <script src="js/lobipanel/lobipanel.min.js"></script>
        <script src="js/iscroll/iscroll.js"></script>
        <script src="js/chart.js/chart.umd.js"></script>
        <script src="js/echarts/echarts.min.js"></script>
        <script src="js/apexcharts/apexcharts.min.js"></script>

        <!-- ========== PAGE JS FILES ========== -->
        <script src="js/prism/prism.js"></script>
        <script src="js/waypoint/waypoints.min.js"></script>
        <script src="js/counterUp/jquery.counterup.min.js"></script>
        <script src="js/amcharts/amcharts.js"></script>
        <script src="js/amcharts/serial.js"></script>
        <script src="js/amcharts/plugins/export/export.min.js"></script>
        <link rel="stylesheet" href="js/amcharts/plugins/export/export.css" type="text/css" media="all" />
        <script src="js/amcharts/themes/light.js"></script>
        <script src="js/toastr/toastr.min.js"></script>
        <script src="js/icheck/icheck.min.js"></script>

        <!-- ========== THEME JS ========== -->
        <script src="js/main.js"></script>
        <script src="js/production-chart.js"></script>
        <script src="js/traffic-chart.js"></script>
        <script src="js/task-list.js"></script>

        <!-- ========== JUST GAGE ========== -->
        <script src="js/justgage.js"></script>
        <script src="js/raphael-2.1.4.min.js"></script>

        <!-- ========== AMCHARTS ========== -->
        <script src="js/amcharts/amcharts.js"></script>
        <script src="js/amcharts/pie.js"></script>
        <script src="js/amcharts/themes/light.js"></script>
<script>
jQuery(document).ready(function() {
    jQuery('#example').DataTable();
});

</script>

<script>
    $(function() {
    // Counter for dashboard stats
        $('.counter').counterUp({
            delay: 10,
            time: 1000
        });

    // Welcome notification
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        toastr["success"]("Welcome to ASMS!");
    });
</script>

<script>
// Initialize JustGage.js
var averageResultsGauge = new JustGage({
    id: "average-results-gauge",
    value: <?php echo $averageResults; ?>,
    min: 0,
    max: 100,
    title: "<?php echo htmlentities($latestExamPeriodName); ?>",
    label: "Average Results",
    gaugeWidthScale: 0.5,
    counter: true,
    pointer: true,
    pointerOptions: {
        toplength: -15,
        bottomlength: 10,
        bottomwidth: 12,
        color: '#8e8e93',
        stroke: '#ffffff',
        stroke_width: 2,
        stroke_linecap: 'round'
    },
    gaugeColor: "#E9ECEF",
    levelColors: ["#F5365C", "#FFD600", "#2DCE89"],
    relativeGaugeSize: true,
    customSectors: [],
    formatNumber: true,
    textRenderer: function(value) {
        return Math.round(value) + '%';
    }
});

// Calculate Attendance Percentage
var presentCount = <?php echo $presentCount; ?>;
var totalStudents = <?php echo $totalstudents; ?>;
var notSubmittedCount = <?php echo $notSubmittedCount; ?>;

var attendancePercentage = 0;
if (totalStudents - notSubmittedCount > 0) {
    attendancePercentage = (presentCount / (totalStudents - notSubmittedCount)) * 100;
}


attendancePercentage = Math.round(attendancePercentage);
markedAttendance = (totalStudents - notSubmittedCount);

// Initialize Attendance Percentage Gauge
var attendancePercentageGauge = new JustGage({
    id: "attendance-percentage-gauge",
    value: attendancePercentage,
    min: 0,
    max: 100,
    title: "Attendance Percentage",
    label: presentCount + "/" + markedAttendance + " Students",
    gaugeWidthScale: 0.5,
    counter: true,
    pointer: true,
    pointerOptions: {
        toplength: -15,
        bottomlength: 10,
        bottomwidth: 12,
        color: '#8e8e93',
        stroke: '#ffffff',
        stroke_width: 2,
        stroke_linecap: 'round'
    },
    gaugeColor: "#E9ECEF",
    levelColors: ["#F5365C", "#FFD600", "#2DCE89"],
    relativeGaugeSize: true,
    customSectors: [],
    formatNumber: true,
    textRenderer: function(value) {
        return Math.round(value) + '%';
    }
});

// Initialize Attendance Marked Gauge
var attendanceMarkedGauge = new JustGage({
    id: "attendance-marked-gauge",
    value: <?php echo ($totalStudents - $notSubmittedCount) / $totalStudents * 100; ?>,
    min: 0,
    max: 100,
    title: "Attendance Marked",
    label: "<?php echo htmlentities($totalstudents - $notSubmittedCount); ?>/<?php echo htmlentities($totalstudents); ?> Students",
    gaugeWidthScale: 0.5,
    counter: true,
    pointer: true,
    pointerOptions: {
        toplength: -15,
        bottomlength: 10,
        bottomwidth: 12,
        color: '#8e8e93',
        stroke: '#ffffff',
        stroke_width: 2,
        stroke_linecap: 'round'
    },
    gaugeColor: "#E9ECEF",
    levelColors: ["#F5365C", "#FFD600", "#2DCE89"],
    relativeGaugeSize: true,
    customSectors: [],
    formatNumber: true,
    textRenderer: function(value) {
        return Math.round(value) + '%';
    }
});

</script>


<script>
    // Replace the following comments with the actual PHP data from the card
    var classAverageData = <?php echo json_encode($resultAvgResultsByClass); ?>;

    // Extract class names and average marks from the PHP result
    var classNames = classAverageData.map(function (data) {
        return data.ClassName;
    });

    var averageMarks = classAverageData.map(function (data) {
        return parseFloat(data.avgMarks.toFixed(2));
    });

    // Create chart for Average Results by Class
    var chart = AmCharts.makeChart("classAverageChart", {
        "type": "serial",
        "theme": "light",
        "dataProvider": classAverageData,
        "valueAxes": [{
            "gridColor": "#ddd",
            "gridAlpha": 1,
            "dashLength": 0
        }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
            "balloonText": "[[category]]: <b>[[value]]</b>",
            "fillAlphas": 0.8,
            "lineAlpha": 0.2,
            "type": "column",
            "valueField": "avgMarks",
            "labelText": "[[value]]"
        }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "ClassName",
        "categoryAxis": {
            "gridPosition": "start",
            "gridAlpha": 0
        },
        "export": {
            "enabled": false
        }
    });
</script>


</body>

</html>
