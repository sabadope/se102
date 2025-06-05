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

            .container {
                max-width: 600px;
                margin: auto;
                background: white;
                padding: 30px;
                box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.05); /* Subtle shadow */
                border-radius: 12px;
                border: 1px solid #e5e7eb; /* Light gray border */
            }

            h1 {
                color: #1e40af; /* Deep blue for headings */
                text-align: center;
                margin-bottom: 10px;
                font-size: 28px;
            }

            p {
                text-align: center;
                color: #6b7280; /* Gray for subtext */
                margin-bottom: 20px;
                font-size: 16px;
            }

            .tabs {
                display: flex;
                justify-content: center;
                margin-bottom: 20px;
            }

            .tab-button {
                padding: 10px 20px;
                margin: 0 5px;
                border: none;
                background: #e5e7eb; /* Light gray for inactive tabs */
                color: #6b7280; /* Gray text */
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                transition: all 0.3s ease;
            }

            .tab-button.active {
                background: #1e40af; /* Deep blue for active tab */
                color: white;
            }

            .tab-button:hover {
                background: #3b82f6; /* Lighter blue on hover */
                color: white;
            }

            .tab-content {
                display: none;
            }

            .tab-content.active {
                display: block;
            }

            .form-group {
                margin-bottom: 15px;
            }

            label {
                font-weight: 600;
                color: #1e40af; /* Deep blue for labels */
                margin-bottom: 5px;
                display: block;
                font-size: 14px;
            }

            input, select, textarea {
                width: 100%;
                padding: 10px;
                margin-top: 5px;
                border: 1px solid #e5e7eb; /* Light gray border */
                border-radius: 6px;
                font-size: 14px;
                background: #f9fafb; /* Light gray background for inputs */
                color: #333; /* Dark gray text */
            }

            textarea {
                resize: vertical;
                min-height: 100px;
            }

            button {
                margin-top: 20px;
                background: #1e40af; /* Deep blue for buttons */
                color: white;
                border: none;
                padding: 12px;
                cursor: pointer;
                border-radius: 6px;
                font-size: 16px;
                transition: background 0.3s ease;
                width: 100%;
            }

            button:hover {
                background: #3b82f6; /* Lighter blue on hover */
            }

            #saved-logs {
                margin-top: 30px;
                padding: 15px;
                background: #f9fafb; /* Light gray background */
                border-radius: 6px;
                border: 1px solid #e5e7eb; /* Light gray border */
            }

            .log-entry {
                margin-bottom: 15px;
                padding: 15px;
                background: white;
                border: 1px solid #e5e7eb; /* Light gray border */
                border-radius: 6px;
                box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05); /* Subtle shadow */
            }

            .log-entry h3 {
                color: #1e40af; /* Deep blue for log titles */
                margin-bottom: 10px;
                font-size: 18px;
            }

            .log-entry p {
                margin: 5px 0;
                color: #6b7280; /* Gray for log content */
                font-size: 14px;
            }

            .log-entry button {
                margin-top: 10px;
                background: #ef4444; /* Red for delete button */
                color: white;
                padding: 8px 12px;
                font-size: 14px;
                width: auto;
            }

            .log-entry button:hover {
                background: #dc2626; /* Darker red on hover */
            }

            .export-btn {
                background: #1e40af;
                color: white;
                border: none;
                padding: 12px;
                cursor: pointer;
                border-radius: 6px;
                font-size: 16px;
                transition: background 0.3s ease;
                width: 100%;
                margin-top: 10px;
            }

            .export-btn:hover {
                background: #3b82f6;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            th, td {
                border: 1px solid #e5e7eb;
                padding: 12px;
                text-align: left;
            }

            th {
                background: #1e40af; 
                color: white;
                font-weight: bold;
            }

            td {
                background: #f9fafb; 
                color: #333;
            }

            form {
                margin-top: 20px;
            }

            form label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }

            form textarea, form select, form input {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            form button {
                background: #1e40af;
                color: white;
                padding: 10px 20px;
                border: none;
                cursor: pointer;
                border-radius: 5px;
            }

            form button:hover {
                background: #3b82f6;
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
                            <h2 class="title">INTERN LOGS</h2>
                        </div>

                        
                        <!-- /.col-sm-6 -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->

                <div class="section">
                    
                    <div class="container">
                        <h1>Intern Performance Logs</h1>
                        <p>Update your daily and weekly tasks here.</p>

                        <!-- Tabs for Daily and Weekly Logs -->
                        <div class="tabs">
                            <button class="tab-button active" onclick="openTab('daily')">Daily Log</button>
                            <button class="tab-button" onclick="openTab('weekly')">Weekly Log</button>
                        </div>

                        <!-- Daily Log Form -->
                        <form action="cha-save_log.php" method="POST" id="daily-log-form" class="tab-content active">
                            <input type="hidden" name="type" value="Daily Log">
                            <div class="form-group">
                                <label>Task Name:</label>
                                <input type="text" name="task_name" required>
                            </div>
                            <div class="form-group">
                                <label>Task Description:</label>
                                <textarea name="task_desc" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Start Time:</label>
                                <input type="time" name="start_time" required>
                            </div>
                            <div class="form-group">
                                <label>End Time:</label>
                                <input type="time" name="end_time" required>
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <select name="status" required>
                                    <option>Completed</option>
                                    <option>In Progress</option>
                                    <option>Pending</option>
                                </select>
                            </div>
                            <button type="submit">Save Daily Log</button>
                            <button onclick="window.location.href='cha-weekly_summary.php'" style="margin-bottom: 20px; background: #1e40af; color: white; border: none; padding: 12px; cursor: pointer; border-radius: 6px; font-size: 16px; transition: background 0.3s ease; width: 100%;">📊 View Weekly Summary</button>

                        </form>

                        <!-- Weekly Log Form -->
                        <form action="cha-save_log.php" method="POST" id="weekly-log-form" class="tab-content">
                            <input type="hidden" name="type" value="Weekly Log">
                            <div class="form-group">
                                <label>Weekly Goals:</label>
                                <textarea name="weekly_goals" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Achievements:</label>
                                <textarea name="achievements" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Challenges:</label>
                                <textarea name="challenges" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Lessons Learned:</label>
                                <textarea name="lessons" required></textarea>
                            </div>
                            <button type="submit">Save Weekly Log</button>
                        </form>

                        <!-- Saved Logs -->
                        <h2>Saved Logs</h2>
                        <button onclick="window.location.href='cha-export_logs.php'" class="export-btn">📤 Export Logs</button>
                        <div id="saved-logs">
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <div class="log-entry">
                                    <h3><?= $row['type'] ?> - <?= $row['timestamp'] ?></h3>
                                    <?= $row['task_name'] ? "<p><strong>Task:</strong> {$row['task_name']}</p>" : "" ?>
                                    <?= $row['weekly_goals'] ? "<p><strong>Weekly Goals:</strong> {$row['weekly_goals']}</p>" : "" ?>
                                    <button onclick="deleteLog(<?= $row['id'] ?>)">🗑 Delete</button>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
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
        <script src="cha-script.js"></script>


        <!-- ========== CHA INTERN ========== -->
        <script>
            function openTab(tabName) {
                document.querySelectorAll(".tab-content").forEach(tab => tab.classList.remove("active"));
                document.querySelectorAll(".tab-button").forEach(button => button.classList.remove("active"));
                document.getElementById(`${tabName}-log-form`).classList.add("active");
                document.querySelector(`button[onclick="openTab('${tabName}')"]`).classList.add("active");
            }

            function deleteLog(id) {
                if (confirm("Are you sure you want to delete this log?")) {
                    window.location.href = `cha-delete_log.php?id=${id}`;
                }
            }
        </script>
        

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
