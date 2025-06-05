<?php include 'db.php'; ?>
<?php
$intern_id = $_GET['id'];
$intern = $conn->query("SELECT * FROM interns WHERE id=$intern_id")->fetch_assoc();
$education = $conn->query("SELECT * FROM education WHERE intern_id=$intern_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Education Background</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3>Education Background for <?= htmlspecialchars($intern['name']) ?></h3>
    <a href="education_add.php?id=<?= $intern_id ?>" class="btn btn-primary mb-3">➕ Add Education</a>
    <a href="index.php" class="btn btn-secondary mb-3">🔙 Back to List</a>

    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-light">
        <tr>
    <th>Degree</th>
    <th>Institution</th>
    <th>Year</th>
    <th>Actions</th>
</tr>

        </thead>
        <tbody>
            <?php while ($row = $education->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['degree']) ?></td>
                    <td><?= htmlspecialchars($row['institution']) ?></td>
                    <td><?= htmlspecialchars($row['year']) ?></td>
                    <td><a href="education_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <td><a href="education_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this record?')" class="btn btn-sm btn-danger">Delete</a>
</td>

                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
