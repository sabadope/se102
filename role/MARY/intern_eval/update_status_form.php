<?php
include 'db_connect.php';

$result = $conn->query("SELECT * FROM hiring_evaluations WHERE status = 'Pending'");
?>

<link rel="stylesheet" href="styles.css">

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Hiring Status</title>
</head>
<body>
    <h2>Update Intern Status</h2>
    <form action="update_status.php" method="POST">
        <select name="id">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?> - <?php echo $row['recommendation']; ?></option>
            <?php } ?>
        </select>
        <select name="status">
            <option value="Approved">✅ Approve</option>
            <option value="Rejected">❌ Reject</option>
        </select>
        <button type="submit">Update</button>
    </form>
</body>
</html>

<?php $conn->close(); ?>
