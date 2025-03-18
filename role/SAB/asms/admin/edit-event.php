<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
    exit;
}

$eventId = $_GET['id'];
$sqlEventDetails = "SELECT * FROM tblupcomingevents WHERE id = :eventId";
$queryEventDetails = $dbh->prepare($sqlEventDetails);
$queryEventDetails->bindParam(':eventId', $eventId, PDO::PARAM_INT);
$queryEventDetails->execute();
$eventDetails = $queryEventDetails->fetch(PDO::FETCH_ASSOC);

if (!$eventDetails) {
    header("Location: index.php");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['eventId'];
    $activityName = $_POST['activityName'];
    $activityDate = $_POST['activityDate'];
    $activityTime = $_POST['activityTime'];
    $eventDescription = $_POST['eventDescription'];

    // Update the database (You need to implement this part)
    $sqlUpdateEvent = "UPDATE tblupcomingevents SET ActivityName = :activityName, EventDate = :activityDate, EventTime = :activityTime, EventDescription = :eventDescription WHERE id = :eventId";
    $queryUpdateEvent = $dbh->prepare($sqlUpdateEvent);
    $queryUpdateEvent->bindParam(':activityName', $activityName, PDO::PARAM_STR);
    $queryUpdateEvent->bindParam(':activityDate', $activityDate, PDO::PARAM_STR);
    $queryUpdateEvent->bindParam(':activityTime', $activityTime, PDO::PARAM_STR);
    $queryUpdateEvent->bindParam(':eventDescription', $eventDescription, PDO::PARAM_STR);
    $queryUpdateEvent->bindParam(':eventId', $eventId, PDO::PARAM_INT);

    if ($queryUpdateEvent->execute()) {
        echo '<div class="alert alert-success" role="alert">Changes saved successfully!</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error updating event. Please try again!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Event</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Event Details</h2>
        <hr>

        <form method="post" action="">
            <input type="hidden" name="eventId" value="<?php echo $eventId; ?>">
            <div class="form-group">
                <label for="activityName">Activity Name:</label>
                <input type="text" class="form-control" id="activityName" name="activityName" value="<?php echo htmlentities($eventDetails['ActivityName']); ?>">
            </div>
            <div class="form-group">
                <label for="activityDate">Date:</label>
                <input type="date" class="form-control" id="activityDate" name="activityDate" value="<?php echo $eventDetails['EventDate']; ?>">
            </div>
            <div class="form-group">
                <label for="activityTime">Time:</label>
                <input type="time" class="form-control" id="activityTime" name="activityTime" value="<?php echo $eventDetails['EventTime']; ?>">
            </div>
            <div class="form-group">
                <label for="eventDescription">Description:</label>
                <textarea class="form-control" id="eventDescription" name="eventDescription"><?php echo isset($_POST['eventDescription']) ? htmlentities($_POST['eventDescription']) : htmlentities($eventDetails['EventDescription']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
</body>

</html>
