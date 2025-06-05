<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
} else {

    if (isset($_POST['submit'])) {
        $periodname = $_POST['periodname'];
        $startdate = $_POST['startdate'];
        $enddate = $_POST['enddate'];
        $status = isset($_POST['status']) ? 1 : 0;
        $periodid = intval($_GET['periodid']);

        // Date validation: Start Date should not be later than End Date
        if (strtotime($startdate) > strtotime($enddate)) {
            $error = "Error: Start Date cannot be later than End Date";
        } else {
            $sql = "UPDATE tblschoolperiods SET PeriodName=:periodname, StartDate=:startdate, EndDate=:enddate, IsActive=:status WHERE id=:periodid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':periodname', $periodname, PDO::PARAM_STR);
            $query->bindParam(':startdate', $startdate, PDO::PARAM_STR);
            $query->bindParam(':enddate', $enddate, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_INT);
            $query->bindParam(':periodid', $periodid, PDO::PARAM_INT);
            $query->execute();
            $msg = "School Period updated successfully";
        }
    }

    // Fetch school period information
    $periodid = intval($_GET['periodid']);
    $sql = "SELECT * FROM tblschoolperiods WHERE id=:periodid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':periodid', $periodid, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Edit School Period</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit School Period</h5>

        <?php if ($msg) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } else if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon me-1"></i>
                <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <!-- Form for updating school period information -->
        <form class="form-horizontal" method="post">
            <div class="row mb-3">
                <label for="periodname" class="col-sm-2 control-label">Period Name</label>
                <div class="col-sm-10">
                    <input type="text" name="periodname" class="form-control" id="periodname" value="<?php echo htmlentities($result->PeriodName); ?>" required="required" autocomplete="off">
                </div>
            </div>
            <div class="row mb-3">
                <label for="startdate" class="col-sm-2 control-label">Start Date</label>
                <div class="col-sm-10">
                    <input type="date" name="startdate" class="form-control" id="startdate" value="<?php echo htmlentities($result->StartDate); ?>" required="required">
                </div>
            </div>
            <div class="row mb-3">
                <label for="enddate" class="col-sm-2 control-label">End Date</label>
                <div class="col-sm-10">
                    <input type="date" name="enddate" class="form-control" id="enddate" value="<?php echo htmlentities($result->EndDate); ?>" required="required">
                </div>
            </div>
            <div class="row mb-3">
                <label for="status" class="col-sm-2 control-label">Status</label>
                <div class="col-sm-10">
                    <input type="checkbox" name="status" value="1" <?php if ($result->IsActive == 1) echo 'checked'; ?>> Active
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" name="submit" class="btn btn-success">Update</button>
                </div>
            </div>
        </form>
        <!-- End of Form -->

    </div>
</div>

<!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
</body>
</html>
<?php } ?>
