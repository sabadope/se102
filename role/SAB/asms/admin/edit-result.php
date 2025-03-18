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

        // Fetch existing results for the selected student
        $sqlResults = "SELECT tblresult.SubjectCode, tblresult.marks, tblsubjects.SubjectName
                        FROM tblresult
                        JOIN tblsubjects ON tblresult.SubjectCode = tblsubjects.SubjectCode
                        WHERE RollId = :rollid";
        $queryResults = $dbh->prepare($sqlResults);
        $queryResults->bindParam(':rollid', $rollid, PDO::PARAM_STR);
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
                    // Update existing marks
                    $sqlUpdateMarks = "UPDATE tblresult
                                       SET marks = :marks
                                       WHERE RollId = :rollid AND SubjectCode = :subjectCode";

                    $queryUpdateMarks = $dbh->prepare($sqlUpdateMarks);
                    $queryUpdateMarks->bindParam(':marks', $marks, PDO::PARAM_STR);
                    $queryUpdateMarks->bindParam(':rollid', $rollid, PDO::PARAM_STR);
                    $queryUpdateMarks->bindParam(':subjectCode', $subjectCode, PDO::PARAM_STR);

                    if ($queryUpdateMarks->execute()) {
                        // Marks updated successfully
                        $msg = "Marks updated successfully for subject: " . $subject['SubjectName'];
                    } else {
                        // Error updating marks
                        $error = "Error updating marks for subject: " . $subject['SubjectName'];
                    }
                } else {
                    // Insert new marks
                    $sqlInsertMarks = "INSERT INTO tblresult (RollId, SubjectCode, marks)
                                       VALUES (:rollid, :subjectCode, :marks)";

                    $queryInsertMarks = $dbh->prepare($sqlInsertMarks);
                    $queryInsertMarks->bindParam(':rollid', $rollid, PDO::PARAM_STR);
                    $queryInsertMarks->bindParam(':subjectCode', $subjectCode, PDO::PARAM_STR);
                    $queryInsertMarks->bindParam(':marks', $marks, PDO::PARAM_STR);

                    if ($queryInsertMarks->execute()) {
                        // Marks inserted successfully
                        $msg = "Marks inserted successfully for subject: " . $subject['SubjectName'];
                    } else {
                        // Error inserting marks
                        $error = "Error inserting marks for subject: " . $subject['SubjectName'];
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
    <title>SMS Admin| Edit Results</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen" >
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen" >
    <link rel="stylesheet" href="css/prism/prism.css" media="screen" >
    <link rel="stylesheet" href="css/select2/select2.min.css" >
    <link rel="stylesheet" href="css/main.css" media="screen" >
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
                    <li class="active">Edit Results</li>
                    <li><?php echo $student['StudentName']; ?> (<?php echo $student['RollId']; ?>)</li>
                    <li><a style="color: #fff" class="btn btn-primary" href="edit-results.php?rollid=<?php echo $student['RollId']; ?>">Confirm Update</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body">
                        <!-- Add your HTML body content here -->
                        <form method="post" action="">
                            <h5>Existing Results:</h5>
                            <div class="form-group">
                                <div>
                                    <ul>
                                        <?php foreach ($results as $result) { ?>
                                            <li style="border-bottom: 1px solid"><?php echo $result['SubjectName'] . ': ' . $result['marks']; ?></li>
                                        <?php } ?>
                                    </ul>
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
                                    <a class="btn btn-primary" href="edit-results.php?rollid=<?php echo $student['RollId']; ?>">Confirm Update</a>
                                </div>
                            </div>
                        </form>
                    </div>
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
