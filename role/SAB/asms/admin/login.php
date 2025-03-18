<?php
session_start();
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user is an admin
    $adminQuery = "SELECT * FROM admin WHERE UserName=:username AND Password=:password";
    $adminStmt = $dbh->prepare($adminQuery);
    $adminStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $adminStmt->bindParam(':password', $password, PDO::PARAM_STR);
    $adminStmt->execute();

    if ($adminStmt->rowCount() > 0) {
        $_SESSION['alogin'] = $username;
        $_SESSION['role'] = 'admin';
        header("Location: admin-dashboard.php");
        exit;
    }

    // Check if the user is a teacher
    $teacherQuery = "SELECT * FROM tblteachers WHERE Email=:username AND Password=:password";
    $teacherStmt = $dbh->prepare($teacherQuery);
    $teacherStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $teacherStmt->bindParam(':password', $password, PDO::PARAM_STR);
    $teacherStmt->execute();

    if ($teacherStmt->rowCount() > 0) {
        $teacherData = $teacherStmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['tlogin'] = $teacherData['Email'];
        $_SESSION['role'] = 'teacher';
        header("Location: teacher-dashboard.php");
        exit;
    }

    // Check if the user is a student
    $studentQuery = "SELECT * FROM tblstudents WHERE RollId=:username AND Password=:password";
    $studentStmt = $dbh->prepare($studentQuery);
    $studentStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $studentStmt->bindParam(':password', $password, PDO::PARAM_STR);
    $studentStmt->execute();

    if ($studentStmt->rowCount() > 0) {
        $studentData = $studentStmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['slogin'] = $studentData['RollId'];
        $_SESSION['role'] = 'student';
        header("Location: student-dashboard.php");
        exit;
    }

    // If no user is found, display an error
    $error = "Invalid username or password";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <!-- Add your CSS links here -->
    <link rel="stylesheet" href="path/to/your/css/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/your/css/style.css">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h2 class="text-center">Login</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</div>

<!-- Add your JS scripts here -->
<script src="path/to/your/js/jquery.min.js"></script>
<script src="path/to/your/js/bootstrap.min.js"></script>
</body>
</html>

