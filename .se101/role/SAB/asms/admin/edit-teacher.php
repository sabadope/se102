<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
    exit();
} else {
    if (isset($_POST['submit'])) {
        $tid = $_GET['tid'];

        // Fetch existing teacher information from the database
        $sqlFetch = "SELECT * FROM tblteachers WHERE id = :tid";
        $queryFetch = $dbh->prepare($sqlFetch);
        $queryFetch->bindParam(':tid', $tid, PDO::PARAM_INT);
        $queryFetch->execute();
        $resultFetch = $queryFetch->fetch(PDO::FETCH_ASSOC);

        // Process the form data
        $tNumber = $_POST['tNumber'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $name = $_POST['name'];
        $password = $_POST['password']; // Raw password from the form
        $securityQuestion = $_POST['securityQuestion'];
        $securityAnswer = $_POST['securityAnswer'];
        $status = isset($_POST['status']) ? 1 : 0;
        $assignedClass = $_POST['assignedClass']; // New field to match

        // Check if a new image is uploaded
        if (!empty($_FILES['profilePicture']['name'])) {
            // Process and save the new image
            $targetDir = "uploads/"; // Set your target directory
            $targetFile = $targetDir . basename($_FILES['profilePicture']['name']);
            move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetFile);
            $profilePicture = $targetFile;
        } else {
            // No new image uploaded, retain the existing image URL
            $profilePicture = $resultFetch['ProfilePicture'];
        }

        // Prepare the SQL update query
        $sqlUpdate = "UPDATE tblteachers SET TNumber = :tNumber, Email = :email, Phone = :phone, Name = :name, SecurityQuestion = :securityQuestion, SecurityAnswer = :securityAnswer, ProfilePicture = :profilePicture, Status = :status";

        // Update the password only if a new one is provided
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $sqlUpdate .= ", Password = :password";
        }

        $sqlUpdate .= " WHERE id = :tid";
        $queryUpdate = $dbh->prepare($sqlUpdate);

        $queryUpdate->bindParam(':tNumber', $tNumber, PDO::PARAM_STR);
        $queryUpdate->bindParam(':email', $email, PDO::PARAM_STR);
        $queryUpdate->bindParam(':phone', $phone, PDO::PARAM_STR);
        $queryUpdate->bindParam(':name', $name, PDO::PARAM_STR);
        $queryUpdate->bindParam(':securityQuestion', $securityQuestion, PDO::PARAM_STR);
        $queryUpdate->bindParam(':securityAnswer', $securityAnswer, PDO::PARAM_STR);
        $queryUpdate->bindParam(':profilePicture', $profilePicture, PDO::PARAM_STR);
        $queryUpdate->bindParam(':status', $status, PDO::PARAM_INT);
        $queryUpdate->bindParam(':tid', $tid, PDO::PARAM_INT);

        // Bind the password parameter if it is set
        if (!empty($password)) {
            $queryUpdate->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        }

        // Execute the update query
        $queryUpdate->execute();

        // Remove the teacher as the class teacher from any previously assigned class
        $sqlRemoveClassTeacher = "UPDATE tblclasses SET TNumber = NULL WHERE TNumber = :tNumber";
        $queryRemoveClassTeacher = $dbh->prepare($sqlRemoveClassTeacher);
        $queryRemoveClassTeacher->bindParam(':tNumber', $tNumber, PDO::PARAM_STR);
        $queryRemoveClassTeacher->execute();

        // Assign the teacher to the selected class if a class is specified
        if (!empty($assignedClass)) {
            $sqlAssignClassTeacher = "UPDATE tblclasses SET TNumber = :tNumber WHERE id = :assignedClass";
            $queryAssignClassTeacher = $dbh->prepare($sqlAssignClassTeacher);
            $queryAssignClassTeacher->bindParam(':tNumber', $tNumber, PDO::PARAM_STR);
            $queryAssignClassTeacher->bindParam(':assignedClass', $assignedClass, PDO::PARAM_INT);
            $queryAssignClassTeacher->execute();
        }

        $msg = "Teacher information updated successfully";
    }

    $tid = $_GET['tid'];

    // Fetch existing teacher information from the database
    $sql = "SELECT id, TNumber, Email, Phone, Name, Password, SecurityQuestion, SecurityAnswer, ProfilePicture, Status FROM tblteachers WHERE id = :tid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':tid', $tid, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

     // Fetch the assigned class for the teacher
    $sqlAssignedClass = "SELECT id FROM tblclasses WHERE TNumber = :tNumber";
    $queryAssignedClass = $dbh->prepare($sqlAssignedClass);
    $queryAssignedClass->bindParam(':tNumber', $result['TNumber'], PDO::PARAM_STR);
    $queryAssignedClass->execute();
    $assignedClassResult = $queryAssignedClass->fetch(PDO::FETCH_ASSOC);
    $assignedClass = $assignedClassResult ? $assignedClassResult['id'] : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Teacher</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
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
                                <h2 class="title">Edit Teacher</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li> Teachers</li>
                                    <li> Edit Teacher</li>
                                    <li class="active"> <?php echo htmlentities($result['Name']); ?> (<?php echo htmlentities($result['TNumber']); ?>)</li>
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
                                                <h5>Edit Teacher Info</h5>
                                            </div>
                                        </div>
                                        <?php if ($msg) { ?>
                                            <div class="alert alert-success left-icon-alert" role="alert">
                                                <strong>Well done!</strong><?php echo htmlentities($msg); ?>
                                            </div>
                                        <?php } ?>
                                        <div class="panel-body p-20">
                                            <!-- Include form fields for teacher info -->
                                            <form method="post" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label for="name">Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlentities($result['Name']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="tNumber">Teacher Number</label>
                                                    <input type="text" class="form-control" id="tNumber" name="tNumber" value="<?php echo htmlentities($result['TNumber']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlentities($result['Email']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="phone">Phone</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlentities($result['Phone']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                    <input type="password" class="form-control" id="password" name="password" value="">
                                                </div>
                                                <div class="form-group">
                                                    <label for="securityQuestion">Security Question</label>
                                                    <input type="text" class="form-control" id="securityQuestion" name="securityQuestion" value="<?php echo htmlentities($result['SecurityQuestion']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="securityAnswer">Security Answer</label>
                                                    <input type="text" class="form-control" id="securityAnswer" name="securityAnswer" value="<?php echo htmlentities($result['SecurityAnswer']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="profilePicture">Profile Picture</label>
                                                    <input type="file" class="form-control" id="profilePicture" name="profilePicture">
                                                    <img src="<?php echo htmlentities($result['ProfilePicture']); ?>" alt="Profile Picture" width="100">
                                                </div>
                                                <div class="form-group">
                                                    <label for="status">Active</label>
                                                    <input type="checkbox" id="status" name="status" value="1" <?php if ($result['Status']) echo 'checked'; ?>>
                                                </div>
                                                <div class="form-group">
                                                    <label for="assignedClass">Assign Class</label>
                                                    <select class="form-control" id="assignedClass" name="assignedClass">
                                                        <option value="">Select Class</option>
                                                        <?php
                                                        // Fetch all classes to populate the dropdown
                                                        $sqlClasses = "SELECT id, ClassName, Section FROM tblclasses";
                                                        $queryClasses = $dbh->prepare($sqlClasses);
                                                        $queryClasses->execute();
                                                        while ($rowClass = $queryClasses->fetch(PDO::FETCH_ASSOC)) {
                                                            // Concatenate ClassName and Section
                                                            $classDisplay = htmlentities($rowClass['ClassName']) . ' (' . htmlentities($rowClass['Section']) . ')';
                                                            $selected = ($assignedClass == $rowClass['id']) ? 'selected' : '';
                                                            echo '<option value="' . htmlentities($rowClass['id']) . '" ' . $selected . '>' . $classDisplay . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <button type="submit" name="submit" class="btn btn-primary">Update</button>
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
