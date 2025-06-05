<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
    exit();
} else {
    $stid = intval($_GET['stid']);

    if (isset($_POST['submit'])) {
        $studentname = $_POST['fullanme'];
        $roolid = $_POST['rollid'];
        $email = $_POST['emailid'];
        $gender = $_POST['gender'];
        $classid = $_POST['class'];
        $dob = $_POST['dob'];
        $status = $_POST['status'];
        $phonenumber = $_POST['phonenumber'];
        $parentname = $_POST['parentname'];
        $security_question = $_POST['security_question'];
        $security_answer = $_POST['security_answer'];
        $sccode = $_POST['sccode'];

        // Check if the password field is empty and handle accordingly
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

        // Check if RollId already exists
        $checkSql = "SELECT COUNT(*) FROM tblstudents WHERE RollId = :roolid AND StudentId != :stid";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':roolid', $roolid, PDO::PARAM_STR);
        $checkQuery->bindParam(':stid', $stid, PDO::PARAM_INT);
        $checkQuery->execute();
        $rollIdExists = $checkQuery->fetchColumn();

        if ($rollIdExists > 0) {
            $error = "The Roll ID already exists. Please use a different Roll ID.";
        } else {
            $target_directory = "uploads/";
            $uploadOk = 1;

            if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["size"] > 0) {
                $target_file = $target_directory . basename($_FILES["profile_picture"]["name"]);

                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                    $profile_picture = $target_file;
                } else {
                    $error = "Sorry, there was an error uploading your file.";
                    error_log("File Upload Error: " . $error);
                }
            } else {
                $profile_picture = $_POST['existing_profile_picture'] ?? '';
            }

            if (!isset($error)) {
                // Prepare the SQL query with or without password update
                $sql = "UPDATE tblstudents 
                        SET StudentName=:studentname, RollId=:roolid, Email=:email, 
                        Gender=:gender, DOB=:dob, ClassId=:classid, Status=:status, 
                        ProfilePicture=:profile_picture, PhoneNumber=:phonenumber, 
                        ParentGuardianName=:parentname, 
                        SecurityQuestion=:security_question, SecurityAnswer=:security_answer,
                        SCCode=:sccode" .
                        (!is_null($password) ? ", Password=:password" : "") . " 
                        WHERE StudentId=:stid";

                $query = $dbh->prepare($sql);
                $query->bindParam(':studentname', $studentname, PDO::PARAM_STR);
                $query->bindParam(':roolid', $roolid, PDO::PARAM_STR);
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->bindParam(':gender', $gender, PDO::PARAM_STR);
                $query->bindParam(':dob', $dob, PDO::PARAM_STR);
                $query->bindParam(':classid', $classid, PDO::PARAM_INT);
                $query->bindParam(':status', $status, PDO::PARAM_INT);
                $query->bindParam(':profile_picture', $profile_picture, PDO::PARAM_STR);
                $query->bindParam(':phonenumber', $phonenumber, PDO::PARAM_STR);
                $query->bindParam(':parentname', $parentname, PDO::PARAM_STR);
                $query->bindParam(':security_question', $security_question, PDO::PARAM_STR);
                $query->bindParam(':security_answer', $security_answer, PDO::PARAM_STR);
                $query->bindParam(':sccode', $sccode, PDO::PARAM_STR);
                $query->bindParam(':stid', $stid, PDO::PARAM_INT);

                if (!is_null($password)) {
                    $query->bindParam(':password', $password, PDO::PARAM_STR);
                }

                $query->execute();

                $msg = "Student info updated successfully";
            }
        }
    }
}

$sql = "SELECT * FROM tblstudents WHERE StudentId=:stid";
$query = $dbh->prepare($sql);
$query->bindParam(':stid', $stid, PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMS Admin | Edit Student</title>
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
        <?php include('includes/topbar.php'); ?>
        <div class="content-wrapper">
            <div class="content-container">
                <?php include('includes/leftbar.php'); ?>
                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row page-title-div">
                            <div class="col-md-6">
                                <h2 class="title">Edit Student</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li> School Period</li>
                                    <li> Edit Student</li>
                                    <li class="active"> <?php echo htmlentities($result->StudentName ?? '') ?> (<?php echo htmlentities($result->RollId ?? '') ?>)</li>
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
                                                <h5>Fill the Student info</h5>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <?php if ($msg){?>
                                                <div class="alert alert-success left-icon-alert" role="alert">
                                                    <strong>Well done!</strong><?php echo htmlentities($msg); ?>
                                                </div><?php } 
                                         else if($error){?>
                                            <div class="alert alert-danger left-icon-alert" role="alert">
                                                <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                            </div>
                                        <?php } ?>
                                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label for="fullanme" class="col-sm-2 control-label">Full Name</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="fullanme" class="form-control" id="fullanme" value="<?php echo htmlentities($result->StudentName ?? '') ?>" required="required" autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="rollid" class="col-sm-2 control-label">Roll Id</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="rollid" class="form-control" id="rollid" value="<?php echo htmlentities($result->RollId ?? '') ?>" maxlength="5" required="required" autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="emailid" class="col-sm-2 control-label">Email id</label>
                                                    <div class="col-sm-10">
                                                        <input type="email" name="emailid" class="form-control" id="email" value="<?php echo htmlentities($result->Email ?? '') ?>" required="required" autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="gender" class="col-sm-2 control-label">Gender</label>
                                                    <div class="col-sm-10">
                                                        <input type="radio" name="gender" value="Male" required="required" <?php echo ($result->Gender == 'Male') ? 'checked' : ''; ?>>Male 
                                                        <input type="radio" name="gender" value="Female" required="required" <?php echo ($result->Gender == 'Female') ? 'checked' : ''; ?>>Female
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="existing_profile_picture" class="col-sm-2 control-label">Existing Profile Picture</label>
                                                    <div class="col-sm-10">
                                                        <?php if (isset($result->ProfilePicture) && !empty($result->ProfilePicture)) : ?>
                                                            <img src="<?php echo htmlentities($result->ProfilePicture) ?>" alt="Profile Picture" width="100">
                                                        <?php else : ?>
                                                            No profile picture available
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="profile_picture" class="col-sm-2 control-label">Update Profile Picture</label>
                                                    <div class="col-sm-10">
                                                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                                                        <input type="hidden" name="existing_profile_picture" value="<?php echo htmlentities($result->ProfilePicture ?? '') ?>">
                                                    </div>
                                                </div>

                                            <div class="form-group">
                                                <label for="class" class="col-sm-2 control-label">Class</label>
                                                <div class="col-sm-10">
                                                    <select name="class" class="form-control" id="class" required="required">
                                                        <?php
                                                        $selectedClassId = isset($result->ClassId) ? htmlentities($result->ClassId) : '';

                                                        if (isset($result->ClassName) && isset($result->Section)) {
                                                            $className = htmlentities($result->ClassName);
                                                            $section = htmlentities($result->Section);
                                                            echo '<option value="' . $selectedClassId . '">' . $className . '(' . $section . ')</option>';
                                                        }

                                                        $classSql = "SELECT * FROM tblclasses";
                                                        $classQuery = $dbh->prepare($classSql);
                                                        $classQuery->execute();
                                                        $classes = $classQuery->fetchAll(PDO::FETCH_OBJ);
                                                        foreach ($classes as $class) {
                                                            echo '<option value="' . $class->id . '">' . $class->ClassName . '(' . $class->Section . ')</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>


                                                <div class="form-group">
                                                    <label for="date" class="col-sm-2 control-label">DOB</label>
                                                    <div class="col-sm-10">
                                                        <input type="date" name="dob" class="form-control" value="<?php echo htmlentities($result->DOB) ?>" id="date">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="regdate" class="col-sm-2 control-label">Reg Date:</label>
                                                    <div class="col-sm-10">
                                                        <?php echo htmlentities($result->RegDate) ?>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="status" class="col-sm-2 control-label">Status</label>
                                                    <div class="col-sm-10">
                                                        <input type="radio" name="status" value="1" required="required" <?php echo ($result->Status == '1') ? 'checked' : ''; ?>>Active 
                                                        <input type="radio" name="status" value="0" required="required" <?php echo ($result->Status == '0') ? 'checked' : ''; ?>>Block 
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="phonenumber" class="col-sm-2 control-label">Phone Number</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="phonenumber" class="form-control" value="<?php echo htmlentities($result->PhoneNumber ?? '') ?>" id="phonenumber" autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="parentname" class="col-sm-2 control-label">Parent/Guardian Name</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="parentname" class="form-control" value="<?php echo htmlentities($result->ParentGuardianName ?? '') ?>" id="parentname" autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="password" class="col-sm-2 control-label">Password</label>
                                                    <div class="col-sm-10">
                                                        <input type="password" name="password" class="form-control" value="<?php echo htmlentities($result->password ?? '') ?>" id="password">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="security_question" class="col-sm-2 control-label">Security Question</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="security_question" class="form-control" value="<?php echo htmlentities($result->SecurityQuestion ?? '') ?>" id="security_question" autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="security_answer" class="col-sm-2 control-label">Security Answer</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="security_answer" class="form-control" value="<?php echo htmlentities($result->SecurityAnswer ?? '') ?>" id="security_answer" autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="sccode" class="col-sm-2 control-label">Subject Combination Code</label>
                                                    <div class="col-sm-10">
                                                        <select name="sccode" class="form-control" id="sccode">
                                                            <option value="<?php echo htmlentities($result->SCCode); ?>"><?php echo htmlentities($result->SCCode); ?></option>
                                                            <?php
                                                            $sccodeSql = "SELECT * FROM tblsccode";
                                                            $sccodeQuery = $dbh->prepare($sccodeSql);
                                                            $sccodeQuery->execute();
                                                            $sccodes = $sccodeQuery->fetchAll(PDO::FETCH_OBJ);
                                                            foreach ($sccodes as $sccode) {
                                                                echo '<option value="' . $sccode->SCCode . '">' . $sccode->SCCode . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-sm-offset-2 col-sm-10">
                                                        <button type="submit" name="submit" class="btn btn-warning">Update</button>
                                                        <a href="delete-student.php" class="btn delete">delete</a>
                                                    </div>
                                                </div>
                                            </form>

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
