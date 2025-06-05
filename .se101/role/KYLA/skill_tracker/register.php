<?php
require 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    $stmt->execute();
    header("Location: login.php");
}
?>

<form method="post">
  Name: <input name="name"><br>
  Email: <input name="email"><br>
  Password: <input type="password" name="password"><br>
  Role:
  <select name="role">
    <option value="intern">Intern</option>
    <option value="supervisor">Supervisor</option>
  </select><br>
  <button type="submit">Register</button>
</form>