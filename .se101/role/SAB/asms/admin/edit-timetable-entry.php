<?php
session_start();
include('includes/config.php');

// Initialize message variables
$msg = '';
$error = '';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $entryId = $_POST['entryId'];
    $classId = $_POST['classId'];
    $day = $_POST['day'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $subjectId = $_POST['subjectId'];
    $teacherTNumber = $_POST['teacherTNumber'];
    $venue = $_POST['venue'];

    // Validate form data
    if ($venue === 'In Class') {
        $venue = -1; // Treat 'In Class' as a specific ID
    } else {
        $venue = intval($venue); // Ensure venue ID is an integer
    }

    // Check if start time is before end time
    if ($startTime >= $endTime) {
        $error = "Start time must be earlier than end time.";
    } else {
        // Check for existing sessions in the selected class
        $sql = "SELECT * FROM tbltimetable 
                WHERE ClassId = :classId 
                AND id != :entryId 
                AND (
                    (StartTime < :endTime AND EndTime > :startTime) 
                    OR (StartTime < :endTime AND EndTime > :endTime) 
                    OR (StartTime < :startTime AND EndTime > :startTime)
                )";

        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':classId' => $classId,
            ':entryId' => $entryId,
            ':startTime' => $startTime,
            ':endTime' => $endTime
        ]);

        if ($stmt->rowCount() > 0) {
            $error = "The selected class already has a session during this time.";
        } else {
            // If venue is not 'In Class', check if it's booked
            if ($venue !== -1) {
                $sql = "SELECT * FROM tbltimetable 
                        WHERE Venue = :venue 
                        AND id != :entryId 
                        AND (
                            (StartTime < :endTime AND EndTime > :startTime) 
                            OR (StartTime < :endTime AND EndTime > :endTime) 
                            OR (StartTime < :startTime AND EndTime > :startTime)
                        )";

                $stmt = $dbh->prepare($sql);
                $stmt->execute([
                    ':venue' => $venue,
                    ':entryId' => $entryId,
                    ':startTime' => $startTime,
                    ':endTime' => $endTime
                ]);

                if ($stmt->rowCount() > 0) {
                    $error = "The selected venue is already booked during this time.";
                }
            }

            // Check if teacher is double-booked
            if (!$error) { // Only proceed if no previous errors
                $sql = "SELECT * FROM tbltimetable 
                        WHERE TeacherTNumber = :teacherTNumber 
                        AND id != :entryId 
                        AND (
                            (StartTime < :endTime AND EndTime > :startTime) 
                            OR (StartTime < :endTime AND EndTime > :endTime) 
                            OR (StartTime < :startTime AND EndTime > :startTime)
                        )";

                $stmt = $dbh->prepare($sql);
                $stmt->execute([
                    ':teacherTNumber' => $teacherTNumber,
                    ':entryId' => $entryId,
                    ':startTime' => $startTime,
                    ':endTime' => $endTime
                ]);

                if ($stmt->rowCount() > 0) {
                    $error = "The selected teacher is already booked during this time.";
                }
            }

            // If no errors, update the timetable entry
            if (!$error) {
                $sql = "UPDATE tbltimetable
                        SET ClassId = :classId, Day = :day, StartTime = :startTime, EndTime = :endTime, 
                            SubjectId = :subjectId, TeacherTNumber = :teacherTNumber, Venue = :venue
                        WHERE id = :entryId";

                $stmt = $dbh->prepare($sql);
                $stmt->execute([
                    ':classId' => $classId,
                    ':day' => $day,
                    ':startTime' => $startTime,
                    ':endTime' => $endTime,
                    ':subjectId' => $subjectId,
                    ':teacherTNumber' => $teacherTNumber,
                    ':venue' => $venue,
                    ':entryId' => $entryId
                ]);

                $msg = "Timetable entry updated successfully.";
            }
        }
    }
}

// Get the timetable entry to edit
$entryId = isset($_GET['entryId']) ? intval($_GET['entryId']) : 0;

$sql = "SELECT * FROM tbltimetable WHERE id = :entryId";
$stmt = $dbh->prepare($sql);
$stmt->execute([':entryId' => $entryId]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch data for dropdowns
$classes = $dbh->query("SELECT id, ClassName, Section FROM tblclasses")->fetchAll(PDO::FETCH_ASSOC);
$subjects = $dbh->query("SELECT id, SubjectName FROM tblsubjects")->fetchAll(PDO::FETCH_ASSOC);
$teachers = $dbh->query("SELECT TNumber, Name FROM tblteachers")->fetchAll(PDO::FETCH_ASSOC);
$venues = $dbh->query("SELECT id, VenueName FROM tblvenues")->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Timetable</title>
    <link rel="stylesheet" href="css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
    <link rel="stylesheet" href="css/main.css" media="screen">
    <script src="js/modernizr/modernizr.min.js"></script>
</head>
<body class="top-navbar-fixed">
    <div class="main-wrapper">

        <!-- ========== TOP NAVBAR ========== -->
        <?php include('includes/topbar.php');?>   
        <!-- ========== WRAPPER FOR BOTH SIDEBARS & MAIN CONTENT ========== -->
        <div class="content-wrapper">
            <div class="content-container">

                <!-- ========== LEFT SIDEBAR ========== -->
                <?php include('includes/leftbar.php');?>                   
                <!-- /.left-sidebar -->

                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row page-title-div">
                            <div class="col-md-6">
                                <h2 class="title">Create Student Class or Venue</h2>
                            </div>
                        </div>
                        <!-- /.row -->
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li><a href="#">Classes</a></li>
                                    <li class="active">Create Class or Venue</li>
                                </ul>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container-fluid -->

                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <div class="panel p-20">
                                        <div class="panel-title">
                                            <h5>Add Class Or Venue</h5>
                                        </div>
                                        <?php if($msg){?>
                                        <div class="alert alert-success left-icon-alert" role="alert">
                                            <strong>Well done!</strong><?php echo htmlentities($msg); ?>
                                        </div><?php } 
                                        else if($error){?>
                                        <div class="alert alert-danger left-icon-alert" role="alert">
                                            <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                        </div>
                                        <?php } ?>
                                        <div class="panel-body">
                                            <div id="addClass" class="tab-content show">
<form action="" method="post">
    <input type="hidden" name="entryId" value="<?php echo htmlentities($entry['id']); ?>">

    <div class="form-group has-success">
        <label for="classId" class="control-label">Class:</label>
        <select id="classId" name="classId" class="form-control" required>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo htmlentities($class['id']); ?>" <?php echo $entry['ClassId'] == $class['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlentities($class['ClassName']); ?> (<?php echo htmlentities($class['Section']); ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group has-success">
        <label for="day" class="control-label">Day:</label>
        <select id="day" name="day" class="form-control" required>
            <option value="">Select Day</option>
            <option value="Monday" <?php if($entry['Day'] == 'Monday') echo 'selected'; ?>>Monday</option>
            <option value="Tuesday" <?php if($entry['Day'] == 'Tuesday') echo 'selected'; ?>>Tuesday</option>
            <option value="Wednesday" <?php if($entry['Day'] == 'Wednesday') echo 'selected'; ?>>Wednesday</option>
            <option value="Thursday" <?php if($entry['Day'] == 'Thursday') echo 'selected'; ?>>Thursday</option>
            <option value="Friday" <?php if($entry['Day'] == 'Friday') echo 'selected'; ?>>Friday</option>
            <option value="Saturday" <?php if($entry['Day'] == 'Saturday') echo 'selected'; ?>>Saturday</option>
            <option value="Sunday" <?php if($entry['Day'] == 'Sunday') echo 'selected'; ?>>Sunday</option>
        </select>
        <span class="help-block">Eg- Monday, Tuesday, etc</span>
    </div>

    <div class="form-group has-success">
        <label for="startTime" class="control-label">Start Time:</label>
        <input type="time" id="startTime" name="startTime" class="form-control" value="<?php echo htmlentities($entry['StartTime']); ?>" required>
    </div>

    <div class="form-group has-success">
        <label for="endTime" class="control-label">End Time:</label>
        <input type="time" id="endTime" name="endTime" class="form-control" value="<?php echo htmlentities($entry['EndTime']); ?>" required>
    </div>

    <div class="form-group has-success">
        <label for="subjectId" class="control-label">Subject:</label>
        <select id="subjectId" name="subjectId" class="form-control" required>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?php echo htmlentities($subject['id']); ?>" <?php echo $entry['SubjectId'] == $subject['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlentities($subject['SubjectName']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group has-success">
        <label for="teacherTNumber" class="control-label">Teacher:</label>
        <select id="teacherTNumber" name="teacherTNumber" class="form-control" required>
            <?php foreach ($teachers as $teacher): ?>
                <option value="<?php echo htmlentities($teacher['TNumber']); ?>" <?php echo $entry['TeacherTNumber'] == $teacher['TNumber'] ? 'selected' : ''; ?>>
                    <?php echo htmlentities($teacher['Name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group has-success">
        <label for="venue" class="control-label">Venue:</label>
        <select id="venue" name="venue" class="form-control">
            <?php foreach ($venues as $venue): ?>
                <option value="<?php echo htmlentities($venue['id']); ?>" <?php echo $entry['Venue'] == $venue['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlentities($venue['VenueName']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <button type="submit" name="submit" class="btn btn-success btn-labeled">
            Update Timetable Entry
            <span class="btn-label btn-label-right"><i class="fa fa-check"></i></span>
        </button>
    </div>
</form>

                             </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col-md-8 col-md-offset-2 -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.container-fluid -->
                    </section>
                    <!-- /.section -->
                </div>
                <!-- /.main-page -->
            </div>
            <!-- /.content-container -->
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- /.main-wrapper -->

    <!-- ========== COMMON JS FILES ========== -->
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/jquery-ui/jquery-ui.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/pace/pace.min.js"></script>
    <script src="js/lobipanel/lobipanel.min.js"></script>
    <script src="js/iscroll/iscroll.js"></script>

    <!-- ========== PAGE JS FILES ========== -->
    <script src="js/prism/prism.js"></script>
    
    <!-- ========== THEME JS ========== -->
    <script src="js/main.js"></script>
</body>
</html>