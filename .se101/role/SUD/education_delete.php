<?php include 'db.php'; ?>
<?php
$id = $_GET['id'];
$result = $conn->query("SELECT intern_id FROM education WHERE id = $id");
$row = $result->fetch_assoc();
$intern_id = $row['intern_id'];

$conn->query("DELETE FROM education WHERE id = $id");
header("Location: education_view.php?id=$intern_id");
?>
