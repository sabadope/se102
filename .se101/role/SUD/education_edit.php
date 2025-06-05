<?php include 'db.php'; ?>
<?php
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM education WHERE id = $id");
$row = $result->fetch_assoc();
$intern_id = $row['intern_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("UPDATE education SET degree=?, institution=?, year=? WHERE id=?");
    $stmt->bind_param("sssi", $_POST['degree'], $_POST['institution'], $_POST['year'], $id);
    $stmt->execute();
    header("Location: education_view.php?id=$intern_id");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Education</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3>Edit Education</h3>
    <form method="post" class="bg-white p-4 shadow rounded">
        <div class="mb-3">
            <label>Degree</label>
            <input type="text" name="degree" value="<?= $row['degree'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Institution</label>
            <input type="text" name="institution" value="<?= $row['institution'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Year</label>
            <input type="text" name="year" value="<?= $row['year'] ?>" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="education_view.php?id=<?= $intern_id ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
