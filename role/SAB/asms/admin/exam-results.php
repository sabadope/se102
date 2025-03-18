<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
    exit;
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SRMS Admin Manage Results</title>
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
                                                <h5>View Students Info</h5>
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
                                            <form method="post" action="view-results.php">
                                                <div class="form-group">
                                                    <label>Select Exam Period:</label>
                                                    <select name="exam_period" class="form-control" required>
                                                        <option value="" disabled selected>Select Exam Period</option>
                                                        <?php
                                                        $sqlExamPeriods = "SELECT * FROM tblexamperiod";
                                                        $queryExamPeriods = $dbh->prepare($sqlExamPeriods);
                                                        $queryExamPeriods->execute();
                                                        $examPeriods = $queryExamPeriods->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($examPeriods as $period) {
                                                        ?>
                                                            <option value="<?php echo $period['id']; ?>"><?php echo $period['PeriodName']; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Select Student:</label>
                                                    <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Select</th>
                                                                <th>#</th>
                                                                <th>Student Name</th>
                                                                <th>Roll Id</th>
                                                                <th>SCCode</th>
                                                                <th>Class</th>
                                                                <th>Section</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $sql = "SELECT tblstudents.StudentName,tblstudents.RollId,tblstudents.SCCode,tblstudents.RegDate,tblstudents.StudentId,tblstudents.Status,tblclasses.ClassName,tblclasses.Section from tblstudents join tblclasses on tblclasses.id=tblstudents.ClassId";
                                                            $query = $dbh->prepare($sql);
                                                            $query->execute();
                                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                            $cnt = 1;
                                                            if ($query->rowCount() > 0) {
                                                                foreach ($results as $result) {
                                                            ?>
                                                                    <tr>
                                                                        <td>
                                                                            <label>
                                                                                <input type="radio" name="student" value="<?php echo htmlentities($result->RollId); ?>" required>
                                                                            </label>
                                                                        </td>
                                                                        <td><?php echo htmlentities($cnt); ?></td>
                                                                        <td><?php echo htmlentities($result->StudentName); ?></td>
                                                                        <td><?php echo htmlentities($result->RollId); ?></td>
                                                                        <td><?php echo htmlentities($result->SCCode); ?></td>
                                                                        <td><?php echo htmlentities($result->ClassName); ?></td>
                                                                        <td><?php echo htmlentities($result->Section); ?></td>
                                                                    </tr>
                                                            <?php
                                                                    $cnt = $cnt + 1;
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button type="submit" name="submit" class="btn btn-primary">View/Edit Results</button>
                                            </form>
                                            <hr>
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
    foreach ($results as $result) {
    ?>
        <div class="modal fade" id="resultsModal<?php echo htmlentities($result->RollId); ?>" tabindex="-1" role="dialog" aria-labelledby="resultsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resultsModalLabel">View/Edit Results</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Use iframe to include view-results.php with the specific RollId -->
                        <iframe id="iframe<?php echo htmlentities($result->RollId); ?>" src="view-results.php?rollid=<?php echo htmlentities($result->RollId); ?>" width="100%" height="520px" frameborder="0"></iframe>
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
        $('#example').DataTable();

        $('#example2').DataTable({
            "scrollY": "300px",
            "scrollCollapse": true,
            "paging": false
        });

        $('#example3').DataTable();
    });

    // Add this script at the end to handle modal opening
    $(document).ready(function () {
        // This function opens the modal when the button is clicked
        function openModal(rollId) {
            $('#resultsModal' + rollId).modal('show');
        }

        // This function closes the modal
        function closeModal(rollId) {
            $('#resultsModal' + rollId).modal('hide');
        }

        // Attach a click event listener to the "View/Edit Results" button
        $('button.view-edit-results').click(function () {
            // Extract the rollId from the button's data-rollid attribute
            var rollId = $(this).data('rollid');
            // Open the modal with the corresponding rollId
            openModal(rollId);
            
            // Load view-results.php in the modal using iframe
            $('#iframe' + rollId).attr('src', 'view-results.php?rollid=' + rollId);
        });
    });
</script>
</body>
</html>
<?php } ?>
