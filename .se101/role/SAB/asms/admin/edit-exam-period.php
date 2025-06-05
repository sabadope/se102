<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['submit'])) {
    $periodId = intval($_GET['periodId']);
    $periodName = $_POST['periodname'];
    $startDate = $_POST['startdate'];
    $endDate = $_POST['enddate'];
    $status = isset($_POST['status']) ? 1 : 0; // Check if the status checkbox is checked

    // Validate that the end date is not earlier than the start date
    if ($endDate < $startDate) {
        $error = "End date cannot be earlier than the start date.";
    } else {
        $sql = "UPDATE tblexamperiod SET PeriodName=:periodName, StartDate=:startDate, EndDate=:endDate, Status=:status WHERE id=:periodId";
        $query = $dbh->prepare($sql);
        $query->bindParam(':periodName', $periodName, PDO::PARAM_STR);
        $query->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $query->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->bindParam(':periodId', $periodId, PDO::PARAM_INT);
        $query->execute();

        $msg = "Exam period updated successfully";
    }
}

$periodId = intval($_GET['periodId']);
if ($periodId <= 0) {
    // Redirect or display an error message if periodId is not provided or invalid
    header("Location: dashboard.php");
    exit;
}

$sql = "SELECT * FROM tblexamperiod WHERE id=:periodId";
$query = $dbh->prepare($sql);
$query->bindParam(':periodId', $periodId, PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMS Admin | Edit Exam Period</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
    <link rel="stylesheet" href="css/select2/select2.min.css">
    <link rel="stylesheet" href="css/main.css" media="screen">
    <script src="js/modernizr/modernizr.min.js"></script>
</head>
<body class="top-navbar-fixed">
<div class="main-wrapper">
    <?php include('includes/topbar.php');?>
    <div class="content-wrapper">
        <div class="content-container">
            <?php include('includes/leftbar.php');?>
            
                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h5>Edit Exam Period</h5>
                                        </div>
                                    </div>
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
                                        <form class="form-horizontal" method="post">
                                            <div class="form-group">
                                                <label for="periodname" class="col-sm-2 control-label">Period Name</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="periodname" class="form-control" id="periodname" value="<?php echo htmlentities($result['PeriodName']) ?>" required="required" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="startdate" class="col-sm-2 control-label">Start Date</label>
                                                <div class="col-sm-10">
                                                    <input type="date" name="startdate" class="form-control" value="<?php echo htmlentities($result['StartDate']) ?>" id="startdate" required="required">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="enddate" class="col-sm-2 control-label">End Date</label>
                                                <div class="col-sm-10">
                                                    <input type="date" name="enddate" class="form-control" value="<?php echo htmlentities($result['EndDate']) ?>" id="enddate" required="required">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="status" class="col-sm-2 control-label">Status</label>
                                                <div class="col-sm-10">
                                                    <input type="checkbox" name="status" value="1" <?php echo ($result['Status'] == 1) ? 'checked' : ''; ?>> Active
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <button type="submit" name="submit" class="btn btn-warning">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
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
