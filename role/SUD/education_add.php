<?php include 'db.php'; ?>
<?php
$intern_id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO education (intern_id, degree, institution, year) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $intern_id, $_POST['degree'], $_POST['institution'], $_POST['year']);
    $stmt->execute();
    header("Location: education_view.php?id=$intern_id");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Education</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Add Education</h2>
    <form method="post" class="bg-white p-4 shadow rounded">
        <div class="mb-3">
            <label>Degree</label>
            <input type="text" name="degree" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Institution</label>
            <input type="text" name="institution" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Year</label>
            <input type="text" name="year" class="form-control" required maxlength="4">
        </div>
        <button type="submit" class="btn btn-success">Add</button>
        <a href="education_view.php?id=<?= $intern_id ?>" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
