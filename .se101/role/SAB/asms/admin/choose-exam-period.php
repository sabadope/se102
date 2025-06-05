<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
} else {
    // Get the rollid parameter from the URL
    $rollid = $_GET['rollid'];

    // Fetch student details based on rollid
    $sql = "SELECT * FROM tblstudents WHERE RollId = :rollid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':rollid', $rollid, PDO::PARAM_STR);
    $query->execute();
    $student = $query->fetch(PDO::FETCH_ASSOC);
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
                            <h2 class="title">Manage Results</h2>
                        </div>
                    </div>
                    <div class="row breadcrumb-div">
                        <div class="col-md-6">
                            <ul class="breadcrumb">
                                <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                <li> Students</li>
                                <li class="active">Manage Results</li>
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
                                            <h5>Available Exam Periods</h5>
                                        </div>
                                    </div>
                                    <div class="panel-body p-20">
                                        <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Period Name</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Fetch and display exam periods
                                                $sql = "SELECT * FROM tblexamperiod";
                                                $query = $dbh->prepare($sql);
                                                $query->execute();
                                                $examPeriods = $query->fetchAll(PDO::FETCH_ASSOC);
                                                $cnt = 1;
                                                foreach ($examPeriods as $examPeriod) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlentities($cnt); ?></td>
                                                        <td><?php echo htmlentities($examPeriod['PeriodName']); ?></td>
                                                        <td><?php echo htmlentities($examPeriod['StartDate']); ?></td>
                                                        <td><?php echo htmlentities($examPeriod['EndDate']); ?></td>
                                                        <td><?php echo ($examPeriod['Status'] == 1) ? 'Active' : 'Inactive'; ?></td>
                                                        <td>
                                                            <?php if ($examPeriod['Status'] == 1) { ?>
                                                                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#resultsModal<?php echo htmlentities($examPeriod['PeriodId']); ?>">
                                                                    View/Edit Results
                                                                </a>
                                                            <?php } else { ?>
                                                                <button class="btn btn-secondary" disabled>
                                                                    View Results
                                                                </button>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $cnt++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
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

<!-- Bootstrap Modal for Viewing/Editing Results -->
<?php
foreach ($examPeriods as $examPeriod) {
?>
    <div class="modal fade" id="resultsModal<?php echo htmlentities($examPeriod['PeriodId']); ?>" tabindex="-1" role="dialog" aria-labelledby="resultsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultsModalLabel">View/Edit Results</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Use iframe to include edit-results.php with the specific RollId and PeriodId -->
                    <iframe src="edit-results.php?rollid=<?php echo htmlentities($rollid); ?>&periodid=<?php echo htmlentities($examPeriod['PeriodId']); ?>" width="100%" height="520px" frameborder="0"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <!-- Add additional buttons if needed -->
                </div>
            </div>
        </div>
    </div>
<?php
}
?>


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
        $('#example').DataTable({
            "scrollY": "300px",
            "scrollCollapse": true,
            "paging": false
        });

        // Rest of your script...
    });
</script>
</body>
</html>
<?php } ?>
