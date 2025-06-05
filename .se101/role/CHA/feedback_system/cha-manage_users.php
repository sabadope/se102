<?php
require_once 'cha-auth_check.php'; // Ensure the user is authenticated and is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: cha-unauthorized.php");
    exit();
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch all users
$users_query = "SELECT id, first_name, last_name, email, role FROM users ORDER BY first_name";
$users_result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'cha-navbar.php'; ?>
    
    <div class="container">
        <h1>Manage Users</h1>
        
        <div class="user-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users_result->num_rows > 0): ?>
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['first_name']) ?></td>
                            <td><?= htmlspecialchars($user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <a href="cha-edit_user.php?id=<?= $user['id'] ?>" class="button">Edit</a>
                                <a href="cha-delete_user.php?id=<?= $user['id'] ?>" class="button danger">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include 'cha-footer.php'; ?>
    <?php $conn->close(); ?>
</body>
</html>