<?php
require 'includes/auth.php';
require 'config/db.php';

$user = $_SESSION['user'];
if ($user['role'] !== 'supervisor') {
    echo "Access denied.";
    exit;
}

$interns = $conn->query("SELECT * FROM users WHERE role='intern'");
if (isset($_GET['id'])) {
    $skills = $conn->query("SELECT * FROM skills WHERE intern_id = {$_GET['id']}");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = $_POST['rating'];
    $skill_id = $_POST['skill_id'];
    $conn->query("UPDATE skills SET supervisor_rating='$rating' WHERE id=$skill_id");
}
?>

<h2>Welcome <?= $user['name'] ?> (Supervisor)</h2>
<a href="logout.php">Logout</a>

<h3>Select Intern</h3>
<ul>
<?php while ($intern = $interns->fetch_assoc()) { ?>
    <li><a href="?id=<?= $intern['id'] ?>"><?= $intern['name'] ?></a></li>
<?php } ?>
</ul>

<?php if (isset($skills)) { ?>
    <h3>Skills</h3>
    <form method="post">
        <?php while ($row = $skills->fetch_assoc()) { ?>
            <p>
              <?= $row['skill_name'] ?> - <?= $row['current_level'] ?><br>
              Rate: <input name="rating" value="<?= $row['supervisor_rating'] ?>"><br>
              <input type="hidden" name="skill_id" value="<?= $row['id'] ?>">
              <button type="submit">Save Rating</button>
            </p>
        <?php } ?>
    </form>
<?php } ?>