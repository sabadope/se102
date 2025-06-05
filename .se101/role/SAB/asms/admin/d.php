<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header("Location: index.php");
    exit();
} else {
    // Fetch active school period for the current date
    $currentDate = date('Y-m-d');
    $sql = "SELECT * FROM tblschoolperiods WHERE StartDate <= :currentDate AND EndDate >= :currentDate";
    $query = $dbh->prepare($sql);
    $query->bindParam(':currentDate', $currentDate, PDO::PARAM_STR);
    $query->execute();
    $periods = $query->fetchAll(PDO::FETCH_ASSOC);

    if (empty($periods)) {
        $error = "No active school period found for the current date.";
    } else {
        $schoolPeriod = $periods[0];
        $startDate = $schoolPeriod['StartDate'];
        $endDate = $schoolPeriod['EndDate'];

        // Fetch all students
        $sql = "SELECT tblstudents.StudentName, tblstudents.RollId, tblstudents.ClassId, tblclasses.ClassName, tblclasses.Section
                FROM tblstudents
                JOIN tblclasses ON tblstudents.ClassId = tblclasses.id";
        $query = $dbh->prepare($sql);
        $query->execute();
        $students = $query->fetchAll(PDO::FETCH_ASSOC);

        // Fetch existing attendance for the selected date
        $attendanceDate = isset($_POST['attendance_date']) ? $_POST['attendance_date'] : $currentDate;
        $attendance = [];
        $sql = "SELECT RollId, IsPresent FROM tblattendance WHERE AttendanceDate = :attendanceDate";
        $query = $dbh->prepare($sql);
        $query->bindParam(':attendanceDate', $attendanceDate, PDO::PARAM_STR);
        $query->execute();
        $existingAttendance = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($existingAttendance as $record) {
            $attendance[$record['RollId']] = $record['IsPresent'];
        }

        // Calculate attendance summary
        $totalStudents = count($students);
        $presentCount = 0;
        $absentCount = 0;
        $notSubmittedCount = $totalStudents - count($attendance);

        foreach ($attendance as $isPresent) {
            if ($isPresent === '1') {
                $presentCount++;
            } elseif ($isPresent === '0') {
                $absentCount++;
            }
        }

        $attendancePercentage = ($totalStudents > 0) ? ($presentCount / $totalStudents) * 100 : 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Summary Card</title>
    <style>
        .class-summary-card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            max-width: 300px;
        }
        .class-summary-card h4 {
            margin-top: 0;
        }
        .class-summary-card .summary {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <?php if (!empty($schoolPeriod)) { ?>
        <div class="class-summary-card">
            <h4>Attendance Summary for <?php echo htmlentities($attendanceDate); ?></h4>
            <p class="summary">
                Total Students: <?php echo htmlentities($totalStudents); ?><br>
                Present Students: <?php echo htmlentities($presentCount); ?><br>
                Absent Students: <?php echo htmlentities($absentCount); ?><br>
                Attendance Not Submitted: <?php echo htmlentities($notSubmittedCount); ?><br>
                Attendance Percentage: <?php echo number_format($attendancePercentage, 2); ?>%
            </p>
        </div>
    <?php } else { ?>
        <div class="alert alert-danger left-icon-alert" role="alert">
            <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
        </div>
    <?php } ?>
</body>
</html>
