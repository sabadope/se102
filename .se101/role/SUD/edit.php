<?php include 'db.php'; ?>
<?php
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM interns WHERE id=$id");
$intern = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("UPDATE interns SET name=?, email=?, department=?, start_date=? WHERE id=?");
    $stmt->bind_param("ssssi", $_POST['name'], $_POST['email'], $_POST['department'], $_POST['start_date'], $id);
    $stmt->execute();
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Intern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Edit Intern</h2>
    <form method="post" class="shadow p-4 bg-white rounded">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="<?= $intern['name'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="<?= $intern['email'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Department</label>
            <input type="text" name="department" value="<?= $intern['department'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Start Date</label>
            <input type="date" name="start_date" value="<?= $intern['start_date'] ?>" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Intern</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
