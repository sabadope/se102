<?php include 'db.php';
$id = $_GET['id'];
$intern = $conn->query("SELECT * FROM interns WHERE id = $id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Intern Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2><?= $intern['name'] ?>'s Profile</h2>

    <ul class="nav nav-tabs mt-4" id="profileTabs" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#personal">Personal</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#education">Education</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#certs">Certifications</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#uploads">Uploads</a></li>
    </ul>

    <div class="tab-content bg-white p-4 border border-top-0 rounded-bottom shadow">
        <div class="tab-pane fade show active" id="personal">
            <p><strong>Email:</strong> <?= $intern['email'] ?></p>
            <p><strong>Department:</strong> <?= $intern['department'] ?></p>
            <p><strong>Start Date:</strong> <?= $intern['start_date'] ?></p>
        </div>
        <div class="tab-pane fade" id="education">
            <?php
            $edu = $conn->query("SELECT * FROM education WHERE intern_id = $id");
            if ($edu->num_rows > 0) {
                while ($e = $edu->fetch_assoc()) {
                    echo "<p><strong>{$e['degree']}</strong> - {$e['institution']} ({$e['year']})</p>";
                }
            } else {
                echo "<p>No education records.</p>";
            }
            ?>
        </div>
        <div class="tab-pane fade" id="certs">
            <p>✨ Coming soon: certifications list here!</p>
        </div>
        <div class="tab-pane fade" id="uploads">
            <p>📁 Coming soon: resume / document uploads here!</p>
        </div>
    </div>

    <a href="index.php" class="btn btn-secondary mt-4">⬅ Back</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
