<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
} else {
    if (isset($_GET['scid'])) {
        $scid = intval($_GET['scid']);

        // Fetch subject combination details
        $sql = "SELECT * FROM tblsccode WHERE id = :scid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':scid', $scid, PDO::PARAM_INT);
        $query->execute();
        $subjectCombination = $query->fetch(PDO::FETCH_ASSOC);

        // Fetch associated subjects
        $sqlSubjects = "SELECT SubjectCode FROM tblsubjectcombination WHERE SCCode = :scCode";
        $querySubjects = $dbh->prepare($sqlSubjects);
        $querySubjects->bindParam(':scCode', $subjectCombination['SCCode'], PDO::PARAM_STR);
        $querySubjects->execute();
        $associatedSubjects = $querySubjects->fetchAll(PDO::FETCH_COLUMN);

        if (isset($_POST['updateSubjectCombination'])) {
            $newSCCode = $_POST['subjectCombinationCode'];

            // Update subject combination code
            $updateSql = "UPDATE tblsccode SET SCCode = :newSCCode WHERE id = :scid";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->bindParam(':newSCCode', $newSCCode, PDO::PARAM_STR);
            $updateQuery->bindParam(':scid', $scid, PDO::PARAM_INT);
            $updateQuery->execute();

            // Remove associated subjects
            if (isset($_POST['subjects'])) {
                $selectedSubjects = $_POST['subjects'];
                // Remove all associated subjects first
                $deleteAllSubjectsSql = "DELETE FROM tblsubjectcombination WHERE SCCode = :scCode";
                $deleteAllSubjectsQuery = $dbh->prepare($deleteAllSubjectsSql);
                $deleteAllSubjectsQuery->bindParam(':scCode', $subjectCombination['SCCode'], PDO::PARAM_STR);
                $deleteAllSubjectsQuery->execute();

                // Add back the checked subjects
                foreach ($selectedSubjects as $selectedSubject) {
                    // Insert subject into tblsubjectcombination
                    $insertSubjectSql = "INSERT INTO tblsubjectcombination(SCCode, SubjectCode) VALUES (:scCode, :subjectCode)";
                    $insertSubjectQuery = $dbh->prepare($insertSubjectSql);
                    $insertSubjectQuery->bindParam(':scCode', $subjectCombination['SCCode'], PDO::PARAM_STR);
                    $insertSubjectQuery->bindParam(':subjectCode', $selectedSubject, PDO::PARAM_STR);
                    $insertSubjectQuery->execute();
                }
            }

            // Display success message
            $successMessage = "Subject combination updated successfully";
        }
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Edit Subject Combination</title>
        <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
        <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
        <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
        <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
        <link rel="stylesheet" href="css/prism/prism.css" media="screen">
        <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.min.css"/>
        <link rel="stylesheet" href="css/main.css" media="screen">
        <script src="js/modernizr/modernizr.min.js"></script>
        <style>
            .errorWrap {
                padding: 10px;
                margin: 0 0 20px 0;
                background: #fff;
                border-left: 4px solid #dd3d36;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
                box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            }
            .succWrap{
                padding: 10px;
                margin: 0 0 20px 0;
                background: #fff;
                border-left: 4px solid #5cb85c;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
                box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            }
        </style>
    </head>
    <body class="top-navbar-fixed">
        <div class="main-wrapper">
            <?php include('includes/topbar.php');?>
            <div class="content-wrapper">
                <div class="content-container">
                    <?php include('includes/leftbar.php');?>
                    <div class="main-page">
                        <div class="container-fluid">
                            <div class="row page-title-div">
                                <div class="col-md-6">
                                    <h2 class="title">Edit Subject Combination</h2>
                                </div>
                            </div>
                            <div class="row breadcrumb-div">
                                <div class="col-md-6">
                                    <ul class="breadcrumb">
                                        <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                        <li><a href="manage-subjectcombination.php"> Subjects Combination</a></li>
                                        <li class="active">Edit Subject Combination</li>
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
                                                    <h5>Edit Subject Combination</h5>
                                                </div>
                                            </div>
                                            <div class="panel-body p-20">

                                                <?php if (isset($successMessage)) { ?>
                                                    <div class="alert alert-success left-icon-alert" role="alert">
                                                        <strong>Success!</strong> <?php echo htmlentities($successMessage); ?>
                                                    </div>
                                                <?php } ?>

                                                <form action="" method="post">
                                                    <div class="form-group">
                                                        <label for="subjectCombinationCode">Subject Combination Code</label>
                                                        <input type="text" class="form-control" id="subjectCombinationCode" name="subjectCombinationCode" value="<?php echo isset($_POST['subjectCombinationCode']) ? htmlentities($_POST['subjectCombinationCode']) : htmlentities($subjectCombination['SCCode']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Associated Subjects</label>
                                                        <?php
                                                        $sqlAllSubjects = "SELECT SubjectCode, SubjectName FROM tblsubjects";
                                                        $queryAllSubjects = $dbh->prepare($sqlAllSubjects);
                                                        $queryAllSubjects->execute();
                                                        $allSubjects = $queryAllSubjects->fetchAll(PDO::FETCH_ASSOC);

                                                        foreach ($allSubjects as $subject) {
                                                            $subjectCode = $subject['SubjectCode'];
                                                            $subjectName = $subject['SubjectName'];
                                                            $subjectDisplayName = "$subjectCode - $subjectName";
                                                            $checked = (isset($_POST['subjects']) && in_array($subjectCode, $_POST['subjects'])) ? 'checked' : (in_array($subjectCode, $associatedSubjects) ? 'checked' : '');
                                                            ?>
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="subjects[]" value="<?php echo $subjectCode; ?>" <?php echo $checked; ?>>
                                                                    <?php echo $subjectDisplayName; ?>
                                                                </label>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <button type="submit" name="updateSubjectCombination" class="btn btn-primary">Update</button>
                                                </form>
                                                <a href="manage-subjectcombination.php" class="btn btn-default">Back</a>
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
        <script src="js/jquery/jquery-2.2.4.min.js"></script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
        <script src="js/pace/pace.min.js"></script>
        <script src="js/lobipanel/lobipanel.min.js"></script>
        <script src="js/iscroll/iscroll.js"></script>
        <script src="js/prism/prism.js"></script>
        <script src="js/DataTables/datatables.min.js"></script>
        <script src="js/main.js"></script>
    </body>
    </html>
<?php } ?>
