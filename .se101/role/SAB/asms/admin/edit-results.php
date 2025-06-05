<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
} else {
    if (isset($_GET['rollid'])) {
        $rollid = $_GET['rollid'];

        // Fetch student information
        $sqlStudent = "SELECT * FROM tblstudents WHERE RollId = :rollid";
        $queryStudent = $dbh->prepare($sqlStudent);
        $queryStudent->bindParam(':rollid', $rollid, PDO::PARAM_STR);
        $queryStudent->execute();
        $student = $queryStudent->fetch(PDO::FETCH_ASSOC);

        // Fetch subjects based on SCCode
        $sqlSubjects = "SELECT tblsubjectcombination.SubjectCode, tblsubjects.SubjectName
                        FROM tblsubjectcombination 
                        JOIN tblsubjects ON tblsubjectcombination.SubjectCode = tblsubjects.SubjectCode
                        WHERE SCCode = :sccode";

        $querySubjects = $dbh->prepare($sqlSubjects);
        $querySubjects->bindParam(':sccode', $student['SCCode'], PDO::PARAM_STR);
        $querySubjects->execute();
        $subjects = $querySubjects->fetchAll(PDO::FETCH_ASSOC);

        // Fetch exam periods
        $sqlExamPeriods = "SELECT * FROM tblexamperiod";
        $queryExamPeriods = $dbh->prepare($sqlExamPeriods);
        $queryExamPeriods->execute();
        $examPeriods = $queryExamPeriods->fetchAll(PDO::FETCH_ASSOC);

        // Fetch existing results for the selected student and exam period
        $selectedPeriod = $_POST['exam_period'] ?? null;
        $sqlResults = "SELECT tblresult.SubjectCode, tblresult.marks, tblsubjects.SubjectName
                        FROM tblresult
                        JOIN tblsubjects ON tblresult.SubjectCode = tblsubjects.SubjectCode
                        WHERE RollId = :rollid AND ExamPeriodId = :selectedPeriod";
        $queryResults = $dbh->prepare($sqlResults);
        $queryResults->bindParam(':rollid', $rollid, PDO::PARAM_STR);
        $queryResults->bindParam(':selectedPeriod', $selectedPeriod, PDO::PARAM_INT);
        $queryResults->execute();
        $results = $queryResults->fetchAll(PDO::FETCH_ASSOC);

        // Process form submission to update or add results
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($subjects as $subject) {
                $subjectCode = $subject['SubjectCode'];
                $marks = isset($_POST[$subjectCode]) ? $_POST[$subjectCode] : null;

                // Check if marks exist for the subject
                $existingResult = array_filter($results, function ($result) use ($subjectCode) {
                    return $result['SubjectCode'] === $subjectCode;
                });

                if ($existingResult) {
                    // Update existing marks for the selected exam period
                    $sqlUpdateMarks = "UPDATE tblresult
                                       SET marks = :marks
                                       WHERE RollId = :rollid AND SubjectCode = :subjectCode AND ExamPeriodId = :selectedPeriod";

                    $queryUpdateMarks = $dbh->prepare($sqlUpdateMarks);
                    $queryUpdateMarks->bindParam(':marks', $marks, PDO::PARAM_STR);
                    $queryUpdateMarks->bindParam(':rollid', $rollid, PDO::PARAM_STR);
                    $queryUpdateMarks->bindParam(':subjectCode', $subjectCode, PDO::PARAM_STR);
                    $queryUpdateMarks->bindParam(':selectedPeriod', $selectedPeriod, PDO::PARAM_INT);

                    if ($queryUpdateMarks->execute()) {
                        // Marks updated successfully
                        $msg = "Marks updated successfully";
                    } else {
                        // Error updating marks
                        $error = "Error updating marks";
                    }
                } else {
                    // Insert new marks for the selected exam period
                    $sqlInsertMarks = "INSERT INTO tblresult (RollId, SubjectCode, marks, ExamPeriodId)
                                       VALUES (:rollid, :subjectCode, :marks, :selectedPeriod)";

                    $queryInsertMarks = $dbh->prepare($sqlInsertMarks);
                    $queryInsertMarks->bindParam(':rollid', $rollid, PDO::PARAM_STR);
                    $queryInsertMarks->bindParam(':subjectCode', $subjectCode, PDO::PARAM_STR);
                    $queryInsertMarks->bindParam(':marks', $marks, PDO::PARAM_STR);
                    $queryInsertMarks->bindParam(':selectedPeriod', $selectedPeriod, PDO::PARAM_INT);

                    if ($queryInsertMarks->execute()) {
                        // Marks inserted successfully
                        $msg = "Marks inserted successfully";
                    } else {
                        // Error inserting marks
                        $error = "Error inserting marks";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SRMS Admin | Edit Results</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
    <link rel="stylesheet" href="css/select2/select2.min.css">
    <link rel="stylesheet" href="css/main.css" media="screen">
    <script src="js/modernizr/modernizr.min.js"></script>
</head>
<body>

<div class="main-wrapper">

    <div class="container-fluid">
        <div class="row page-title-div">
            <div class="col-md-6">
                <h2 class="title">View / Edit Results</h2>
            </div>
        </div>
        <div class="row breadcrumb-div">
            <div class="col-md-6">
                <ul class="breadcrumb">
                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                    <li class="active">Student Result</li>
                    <li><?php echo $student['StudentName']; ?> (<?php echo $student['RollId']; ?>)</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body">
                        <?php if ($msg) { ?>
                            <div class="alert alert-success left-icon-alert" role="alert">
                                <strong>Well done!</strong><?php echo htmlentities($msg); ?>
                            </div>
                        <?php } else if ($error) { ?>
                            <div class="alert alert-danger left-icon-alert" role="alert">
                                <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                            </div>
                        <?php } ?>
                        <form method="post" action="">
                            <!-- Add your HTML body content here -->
                            <h5>Exam Period:</h5>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Exam Period:</label>
                                <div class="col-sm-10">
                                    <select name="exam_period" class="form-control">
                                        <?php foreach ($examPeriods as $period) { ?>
                                            <option value="<?php echo $period['id']; ?>" <?php if ($selectedPeriod == $period['id']) echo 'selected'; ?>><?php echo $period['period_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <h5>Add / Update Results:</h5>
                            <?php foreach ($subjects as $subject) {
                                $subjectCode = $subject['SubjectCode'];
                                $existingMarks = array_reduce($results, function ($carry, $result) use ($subjectCode) {
                                    if ($result['SubjectCode'] === $subjectCode) {
                                        $carry = $result['marks'];
                                    }
                                    return $carry;
                                });
                                ?>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?php echo $subject['SubjectName']; ?>:</label>
                                    <div class="col-sm-10">
                                        <div id="reslt">
                                            <input type="text" name="<?php echo $subjectCode; ?>" 
                                                value="<?php echo $existingMarks; ?>" 
                                                placeholder="Enter marks out of 100" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" name="submit" class="btn btn-primary">Update Results</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/pace/pace.min.js"></script>
    <script src="js/lobipanel/lobipanel.min.js"></script>
    <script src="js/iscroll/iscroll.js"></script>
    <script src="js/prism/prism.js"></script>
    <script src="js/select2/select2.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        $(function ($) {
            $(".js-states").select2();
            $(".js-states-limit").select2({
                maximumSelectionLength: 2
            });
            $(".js-states-hide").select2({
                minimumResultsForSearch: Infinity
            });
        });
    </script>
</body>
</html>
