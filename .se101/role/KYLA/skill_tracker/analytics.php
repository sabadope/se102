<?php
require 'includes/auth.php';
require 'config/db.php';

if ($_SESSION['user']['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

$result = $conn->query("
  SELECT u.name, s.skill_name, s.initial_level, s.current_level, s.supervisor_rating
  FROM skills s
  JOIN users u ON s.intern_id = u.id
");

echo "<h2>Skill Development Analytics</h2><table border='1'><tr><th>Intern</th><th>Skill</th><th>Initial</th><th>Current</th><th>Supervisor Rating</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['name']}</td>
        <td>{$row['skill_name']}</td>
        <td>{$row['initial_level']}</td>
        <td>{$row['current_level']}</td>
        <td>{$row['supervisor_rating']}</td>
    </tr>";
}
echo "</table>";