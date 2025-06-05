<?php
session_start();
include 'jv-db.php'; // Database connection

// Ensure user is logged in and is a supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header('Location: jv-login.php');
    exit;
}

// Fetch all interns to select from
$sql = "SELECT id, username FROM users WHERE role = 'intern'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$interns = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $date = $_POST['date'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $status = $_POST['status'];

    // Insert the new attendance record into the database
    $sql = "INSERT INTO attendance (user_id, date, check_in, check_out, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $date, $check_in, $check_out, $status]);

    // Redirect back to the supervisor dashboard after successful submission
    header('Location: jv-supervisor_dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Attendance</title>
    <style>
        /* Basic styles */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(145deg, #e3f2fd, #f1f8e9);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            font-size: 16px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;
            font-size: 24px;
            font-weight: bold;
        }

        label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            display: block;
            font-size: 14px;
        }

        input, select, button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        input:focus, select:focus, button:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-link {
            display: inline-block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            font-size: 14px;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 15px;
        }

        /* Ensure consistency in width */
        .form-group select, .form-group input {
            width: 100%;
            padding-left: 10px; /* Add left padding for consistency */
        }

        /* Focus effect */
        .form-group input[type="date"],
        .form-group input[type="time"] {
            width: 100%;
            padding-left: 10px;
        }

        /* Adjustments for better spacing */
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group select, .form-group input {
            margin-top: 5px;
        }

        /* Additional responsive layout fixes */
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            h2 {
                font-size: 20px;
            }

            .back-link {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Add New Attendance</h2>

        <form method="POST" action="jv-add_attendance.php">
            
            <!-- Intern Selection -->
            <div class="form-group">
                <label for="user_id">Select Intern:</label>
                <select name="user_id" required>
                    <option value="">-- Select Intern --</option>
                    <?php foreach ($interns as $intern): ?>
                        <option value="<?php echo $intern['id']; ?>"><?php echo htmlspecialchars($intern['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Date -->
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" name="date" required>
            </div>

            <!-- Check-in Time -->
            <div class="form-group">
                <label for="check_in">Check-in Time:</label>
                <input type="time" name="check_in" required>
            </div>

            <!-- Check-out Time -->
            <div class="form-group">
                <label for="check_out">Check-out Time:</label>
                <input type="time" name="check_out" required>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" required>
                    <option value="present">Present</option>
                    <option value="late">Late</option>
                    <option value="absent">Absent</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit">Add Attendance</button>
        </form>

        <a href="jv-supervisor_dashboard.php" class="back-link">Cancel</a>
    </div>

</body>
</html>
