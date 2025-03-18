<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])=="")
{   
    header("Location: index.php"); 
}
else {
    if(isset($_POST['update']))
    {
        $classname = $_POST['classname'];
        $classnamenumeric = $_POST['classnamenumeric']; 
        $section = $_POST['section'];
        $classid = intval($_GET['classid']);
        $teacherNumber = $_POST['teacherNumber'];

        // Check if the selected teacher is already assigned to another class
        $checkSql = "SELECT tblclasses.ClassName, tblclasses.Section, tblteachers.Name as TeacherName
                     FROM tblclasses 
                     JOIN tblteachers ON tblclasses.TNumber = tblteachers.TNumber
                     WHERE tblteachers.TNumber = :teacherNumber AND tblclasses.id != :classid";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':teacherNumber', $teacherNumber, PDO::PARAM_STR);
        $checkQuery->bindParam(':classid', $classid, PDO::PARAM_INT);
        $checkQuery->execute();

        if ($checkQuery->rowCount() > 0) {
            $teacherAssigned = $checkQuery->fetch(PDO::FETCH_OBJ);
            $error = "'{$teacherAssigned->TeacherName}' (TNumber: $teacherNumber) is already assigned to '{$teacherAssigned->ClassName}' ({$teacherAssigned->Section}).";
        } else {
            // Update the class details
            $sql = "UPDATE tblclasses SET ClassName=:classname, ClassNameNumeric=:classnamenumeric, Section=:section, TNumber=:teacherNumber WHERE id=:classid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':classname', $classname, PDO::PARAM_STR);
            $query->bindParam(':classnamenumeric', $classnamenumeric, PDO::PARAM_STR);
            $query->bindParam(':section', $section, PDO::PARAM_STR);
            $query->bindParam(':teacherNumber', $teacherNumber, PDO::PARAM_STR);
            $query->bindParam(':classid', $classid, PDO::PARAM_INT);
            $query->execute();
            $msg = "Data has been updated successfully";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMS Admin Update Class</title>
    <link rel="stylesheet" href="css/bootstrap.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen" >
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen" >
    <link rel="stylesheet" href="css/prism/prism.css" media="screen" >
    <link rel="stylesheet" href="css/main.css" media="screen" >
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
                                <h2 class="title">Update Student Class</h2>
                            </div>
                        </div>
                        <!-- /.row -->
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li><a href="#">Classes</a></li>
                                    <li class="active">Update Class</li>
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
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h5>Update Student Class info</h5>
                                            </div>
                                        </div>
                                        <?php if($msg) { ?>
                                            <div class="alert alert-success left-icon-alert" role="alert">
                                                <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                                            </div>
                                        <?php } else if($error) { ?>
                                            <div class="alert alert-danger left-icon-alert" role="alert">
                                                <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                            </div>
                                        <?php } ?>

                                        <form method="post">
                                            <?php 
                                            $classid = intval($_GET['classid']);
                                            $sql = "SELECT * FROM tblclasses WHERE id = :classid";
                                            $query = $dbh->prepare($sql);
                                            $query->bindParam(':classid', $classid, PDO::PARAM_INT);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);

                                            $sqlTeachers = "SELECT * FROM tblteachers";
                                            $queryTeachers = $dbh->prepare($sqlTeachers);
                                            $queryTeachers->execute();
                                            $teachers = $queryTeachers->fetchAll(PDO::FETCH_OBJ);

                                            if($query->rowCount() > 0) {
                                                foreach($results as $result) {
                                            ?>
                                                <div class="form-group has-success">
                                                    <label for="success" class="control-label">Class Name</label>
                                                    <div class="">
                                                        <input type="text" name="classname" value="<?php echo htmlentities($result->ClassName); ?>" required="required" class="form-control" id="success">
                                                        <span class="help-block">Eg- Third, Fourth, Sixth etc</span>
                                                    </div>
                                                </div>
                                                <div class="form-group has-success">
                                                    <label for="success" class="control-label">Class Name in Numeric</label>
                                                    <div class="">
                                                        <input type="number" name="classnamenumeric" value="<?php echo htmlentities($result->ClassNameNumeric); ?>" required="required" class="form-control" id="success">
                                                        <span class="help-block">Eg- 1, 2, 4, 5 etc</span>
                                                    </div>
                                                </div>
                                                <div class="form-group has-success">
                                                    <label for="success" class="control-label">Section</label>
                                                    <div class="">
                                                        <input type="text" name="section" value="<?php echo htmlentities($result->Section); ?>" class="form-control" required="required" id="success">
                                                        <span class="help-block">Eg- A, B, C etc</span>
                                                    </div>
                                                </div>
                                                <div class="form-group has-success">
                                                    <label for="success" class="control-label">Class Teacher</label>
                                                    <div class="">
                                                        <select name="teacherNumber" class="form-control" id="success">
                                                            <option value="">Select Teacher</option>
                                                            <?php foreach($teachers as $teacher) { 
                                                                $selected = ($teacher->TNumber == $result->TNumber) ? 'selected' : '';
                                                                $disabled = ($teacher->AssignedClass && $teacher->AssignedClass != $result->id) ? 'disabled' : ''; ?>
                                                                <option value="<?php echo htmlentities($teacher->TNumber); ?>" <?php echo $selected . ' ' . $disabled; ?>>
                                                                    <?php echo htmlentities($teacher->Name); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                        <span class="help-block">Select a teacher for this class</span>
                                                    </div>
                                                </div>
                                            <?php }} ?>
                                            <div class="form-group has-success">
                                                <div class="">
                                                    <button type="submit" name="update" class="btn btn-success btn-labeled">Update<span class="btn-label btn-label-right"><i class="fa fa-check"></i></span></button>
                                                </div>
                                            </div>
                                        </form>
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
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/jquery/jquery.validate.min.js"></script>
    <script src="js/lobipanel/lobipanel.min.js"></script>
    <script src="js/pace/pace.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
<?php } ?>