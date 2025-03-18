<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Clear session if already logged in
if($_SESSION['alogin']!=''){
    $_SESSION['alogin']='';
}

// Check if the form is submitted
if(isset($_POST['login']))
{
    $uname=$_POST['username'];
    $password=$_POST['password'];

    // Prepare and execute query to fetch the hashed password for the username
    $sql ="SELECT UserName, Password FROM admin WHERE UserName=:uname";
    $query= $dbh->prepare($sql);
    $query->bindParam(':uname', $uname, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    // If the user is found
    if($result) {
        // Verify the entered password against the hashed password stored in the database
        if(password_verify($password, $result->Password)) {
            $_SESSION['alogin']=$result->UserName;
            echo "<script type='text/javascript'> document.location = 'dashboard.php'; </script>";
        } else {
            echo "<script>alert('Invalid Details');</script>";
        }
    } else {
        echo "<script>alert('Invalid Details');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>School Management System</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen"> <!-- USED FOR DEMO HELP - YOU CAN REMOVE IT -->
    <link rel="stylesheet" href="css/main.css" media="screen">
    <script src="js/modernizr/modernizr.min.js"></script>
    <style>
        body {
            background-color: #f4dcd2; /* White background for the body */
            color: #333; /* Dark font color for better readability */
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            width: 80%;
            max-width: 900px; /* Adjust as needed */
            border-radius: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            background-color: #2d545e;
            color: #f5f5f5; /* Light font color inside the container */
            display: flex;
        }
        .login-container img {
            width: 50%;
            object-fit: cover;
        }
        .login-form {
            width: 50%;
            padding: 2rem;
        }
        .login-form h5 {
            margin-bottom: 1rem;
            color: #f5f5f5; /* Light color for the heading */
        }
        .login-form .form-control {
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .form-header {
            font-weight: 800;
            color: #f5f5f5;
        }
        .login-form .btn {
            border-radius: 0.5rem;
        }
        @media (max-width: 768px) {
            .login-container img {
                display: none;
            }
            .login-form {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <img class="form-image" src="images/photo-1.jpg" alt="Background Image">
    <div class="login-form">
        <form method="post">
            <h2 class="text-center mb-4 form-header">ASMS - ADMIN LOGIN</h2>
            <h5 class="fw-normal mb-3 pb-3">Sign into your account</h5>

            <div class="form-outline mb-4">
                <label class="form-label" for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control form-control-lg" required placeholder="Username">
            </div>

            <div class="form-outline mb-4">
                <label class="form-label" for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control form-control-lg" required placeholder="Password">
            </div>

            <div class="pt-1 mb-4 text-right">
                <button type="submit" name="login" class="btn btn-success btn-labeled">Login<span class="btn-label btn-label-right"><i class="fa fa-check"></i></span></button>
            </div>
            <br>
            <br>
            <p class="mb-5 pb-lg-2"><a href="#!" style="color: #f5f5f5">Forgot password?</a></p>
            <a href="#!" class="small " style="color: #f5f5f5">Terms of use.</a>
            <a href="#!" class="smalld" style="color: #f5f5f5">Privacy policy</a>
        </form>
    </div>
</div>

<!-- ========== COMMON JS FILES ========== -->
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/jquery-ui/jquery-ui.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/pace/pace.min.js"></script>
<script src="js/lobipanel/lobipanel.min.js"></script>
<script src="js/iscroll/iscroll.js"></script>

<!-- ========== PAGE JS FILES ========== -->

<!-- ========== THEME JS ========== -->
<script src="js/main.js"></script>
<script>
    $(function() {
        // Custom scripts if needed
    });
</script>

<!-- ========== ADD custom.js FILE BELOW WITH YOUR CHANGES ========== -->
</body>
</html>
