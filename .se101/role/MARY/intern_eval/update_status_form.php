<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $recommendation = $_POST["recommendation"];
    $sql = "UPDATE interns SET recommendation='$recommendation' WHERE id=$id";
    $conn->query($sql);
}

$result = $conn->query("SELECT * FROM hiring_evaluations");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Intern Status</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Update Hiring Recommendation</h2>
    <form method="post">
        <label for="id">Select Intern:</label>
        <select name="id" required>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
            <?php } ?>
        </select>

        <label for="recommendation">Recommendation:</label>
        <select name="recommendation" required>
            <option value="Recommended">Recommended</option>
            <option value="Needs Review">Needs Review</option>
            <option value="Not Recommended">Not Recommended</option>
        </select>

        <button type="submit">Update</button>
    </form>
</body>
</html>
