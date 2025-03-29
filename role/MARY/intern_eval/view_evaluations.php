<?php
include 'db_connect.php';

$result = $conn->query("SELECT * FROM hiring_evaluations ORDER BY last_updated DESC");
?>

<link rel="stylesheet" href="styles.css">

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Intern Evaluations</title>
</head>
<body>
    <h2>Intern Evaluations</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Intern Name</th>
            <th>Total Score</th>
            <th>Behavior Score</th>
            <th>Hiring Score</th>
            <th>Recommendation</th>
            <th>Status</th>
            <th>Last Updated</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['total_score']; ?></td>
            <td><?php echo $row['behavior_score']; ?></td>
            <td><?php echo $row['hiring_score']; ?></td>
            <td><?php echo $row['recommendation']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo $row['last_updated']; ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
