<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin']) == "")
{   
    header("Location: index.php"); 
}
else
{

    $msg = "";
    $error = "";

    
    // Handle form submission for adding class
    if(isset($_POST['submit_class']))
    {
        $classname = $_POST['classname'];
        $classnamenumeric = $_POST['classnamenumeric']; 
        $section = $_POST['section'];

        // Check if class already exists
        $checkSql = "SELECT * FROM tblclasses WHERE ClassName = :classname AND ClassNameNumeric = :classnamenumeric AND Section = :section";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':classname', $classname, PDO::PARAM_STR);
        $checkQuery->bindParam(':classnamenumeric', $classnamenumeric, PDO::PARAM_STR);
        $checkQuery->bindParam(':section', $section, PDO::PARAM_STR);
        $checkQuery->execute();
        if ($checkQuery->rowCount() > 0) {
            $error = "Class already exists.";
        } else {
            // Insert new class
            $sql = "INSERT INTO tblclasses (ClassName, ClassNameNumeric, Section) VALUES (:classname, :classnamenumeric, :section)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':classname', $classname, PDO::PARAM_STR);
            $query->bindParam(':classnamenumeric', $classnamenumeric, PDO::PARAM_STR);
            $query->bindParam(':section', $section, PDO::PARAM_STR);
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();
            if($lastInsertId)
            {
                $msg = "Class created successfully";
            }
            else 
            {
                $error = "Something went wrong. Please try again";
            }
        }
    }

    // Handle form submission for adding venue
    if(isset($_POST['submit_venue']))
    {
        $venuename = $_POST['venuename'];

        // Check if venue already exists
        $checkSql = "SELECT * FROM tblvenues WHERE VenueName = :venuename";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':venuename', $venuename, PDO::PARAM_STR);
        $checkQuery->execute();
        if ($checkQuery->rowCount() > 0) {
            $error = "Venue already exists.";
        } else {
            // Insert new venue
            $sql = "INSERT INTO tblvenues (VenueName) VALUES (:venuename)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':venuename', $venuename, PDO::PARAM_STR);
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();
            if($lastInsertId)
            {
                $msg = "Venue created successfully";
            }
            else 
            {
                $error = "Something went wrong. Please try again";
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
    <title>SRMS Admin Create Class</title>
    <link rel="stylesheet" href="css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
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
        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .tab {
            cursor: pointer;
            text-align: center;
            transition: 0.3s;
        }
        .tab-content {
            display: none;
            padding: 6px 12px;
        }
        .nav-tabs li a.active {
            color: #fff;
            background-color: #337ab7;
            border: 1px solid #ddd;
            border-bottom-color: transparent;
            opacity:1;
        }
        .tab-content.show {
            display: block;
        }
    </style>
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
                                          <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="bi bi-check-circle me-1"></i>
                                            <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                          </div><?php } 
                                           else if($error){?>

                                          <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="bi bi-exclamation-octagon me-1"></i>
                                            <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                          </div>
                                        <?php } ?>

                                        <div class="panel-heading">
                                            <ul id="tabs" class="nav nav-tabs">
                                                <li><a class="tab active" onclick="openTab(event, 'addClass')">Add Class</a></li>
                                                <li><a class="tab" onclick="openTab(event, 'addVenue')">Add Venue</a></li>
                                            </ul>
                                        </div>
                                        <div class="panel-body">
                                            <div id="addClass" class="tab-content show">
                                                <form method="post">
                                                    <input type="hidden" name="type" value="In Class">
                                                    <div class="form-group has-success">
                                                        <label for="success" class="control-label">Class Name</label>
                                                        <div class="">
                                                            <input type="text" name="classname" class="form-control" required="required" id="success">
                                                            <span class="help-block">Eg- Third, Fourth, Sixth etc</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group has-success">
                                                        <label for="success" class="control-label">Class Name in Numeric</label>
                                                        <div class="">
                                                            <input type="number" name="classnamenumeric" required="required" class="form-control" id="success">
                                                            <span class="help-block">Eg- 1,2,4,5 etc</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group has-success">
                                                        <label for="success" class="control-label">Section</label>
                                                        <div class="">
                                                            <input type="text" name="section" class="form-control" id="success">
                                                            <span class="help-block">Eg- A,B,C etc</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group has-success">
                                                        <div class="">
                                                            <button type="submit" name="submit_class" class="btn btn-success btn-labeled">Submit<span class="btn-label btn-label-right"><i class="fa fa-check"></i></span></button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div id="addVenue" class="tab-content">
                                                <form method="post">
                                                    <div class="form-group has-success">
                                                        <label for="success" class="control-label">Venue Name</label>
                                                        <div class="">
                                                            <input type="text" name="venuename" class="form-control" required="required" id="success">
                                                        </div>
                                                    </div>
                                                    <div class="form-group has-success">
                                                        <div class="">
                                                            <button type="submit" name="submit_venue" class="btn btn-success btn-labeled">Submit<span class="btn-label btn-label-right"><i class="fa fa-check"></i></span></button>
                                                        </div>
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

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;

            // Get all elements with class="tab-content" and hide them
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("show");
            }

            // Get all elements with class="tab" and remove the class "active"
            tablinks = document.getElementsByClassName("tab");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }

            // Show the current tab, and add an "active" class to the button that opened the tab
            document.getElementById(tabName).classList.add("show");
            evt.currentTarget.classList.add("active");
        }
    </script>
</body>
</html>
<?php } ?>