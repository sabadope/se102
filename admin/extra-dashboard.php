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
