<?php
require 'includes/auth.php';
require 'config/db.php';

$user = $_SESSION['user'];
if ($user['role'] !== 'intern') {
    echo "Access denied.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $skill = $_POST['skill'];
    $initial = $_POST['initial'];
    $current = $_POST['current'];
    $stmt = $conn->prepare("INSERT INTO skills (intern_id, skill_name, initial_level, current_level, last_updated) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $user['id'], $skill, $initial, $current);
    $stmt->execute();
}

$skills = $conn->query("SELECT * FROM skills WHERE intern_id = {$user['id']}");
?>

<h2>Welcome <?= $user['name'] ?> (Intern)</h2>
<a href="logout.php">Logout</a>

<form method="post">
  Skill Name: <input name="skill"><br>
  Initial Level: <input name="initial"><br>
  Current Level: <input name="current"><br>
  <button type="submit">Add/Update Skill</button>
</form>

<h3>Your Skills</h3>
<ul>
<?php while ($row = $skills->fetch_assoc()) { ?>
  <li><?= $row['skill_name'] ?> - <?= $row['current_level'] ?> (Updated: <?= $row['last_updated'] ?>)</li>
<?php } ?>
</ul>