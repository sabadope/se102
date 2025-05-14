<?php
session_start();
include 'jv-db.php'; // Database connection

// Ensure user is logged in and is a supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header('Location: jv-login.php');
    exit;
}

// Fetch all attendance records
$sql = "SELECT * FROM attendance ORDER BY date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$attendanceRecords = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard - Attendance</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Target the entire page's scrollbar */
        ::-webkit-scrollbar {
            width: 6px; /* Set the width of the scrollbar */
            height: 6px; /* Set the height of the horizontal scrollbar (if needed) */
        }

        /* Style the track (the background of the scrollbar) */
        ::-webkit-scrollbar-track {
            background: #f1f1f1; /* Light background for the track */
            border-radius: 10px;
        }

        /* Style the thumb (the draggable part of the scrollbar) */
        ::-webkit-scrollbar-thumb {
            background: #888; /* Set the color of the thumb */
            border-radius: 10px; /* Round corners for the thumb */
        }

        /* Hover effect for the thumb */
        ::-webkit-scrollbar-thumb:hover {
            background: #555; /* Darker color when the user hovers over the thumb */
        }
        
        /* Basic Styles */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(145deg, #e3f2fd, #f1f8e9);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-size: 16px;

        }

        .container {
            background-color: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 95%;
            max-width: 1200px; /* Increased width */
            
        }


        /* Header Styles */
        header {
            background-color: rgb(76, 116, 175);
            color: white;
            padding: 15px 0;
            text-align: center;
            border-radius: 8px;
        }

        header h1 {
            margin: 0;
            font-size: 28px;
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 22px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 16px;
            border-radius: 10px; /* Add rounded corners to the table */
            overflow: hidden; /* Ensures the rounded corners are visible */
        }

        th, td {
            padding: 12px;
            text-align: center;
            
            
        }

        th {
            background-color: rgb(76, 116, 175);
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }


        .back-btn, .add-btn {
            display: inline-block;
            padding: 10px 18px;
            background-color: #007BFF;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 18px;
            border: none;
        }

        .back-btn:hover, .add-btn:hover {
            background-color: #0056b3;
        }

        .logout-btn {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 18px;
            background-color: #dc3545;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        /* Modal Styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4); 
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 25px;
            border: 1px solid #888;
            width: 70%;
            max-width: 500px;
            border-radius: 6px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-footer {
            display: flex;
            justify-content: space-between;
            padding-top: 10px;
        }

        .modal-footer button {
            padding: 8px 14px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
        }

        .modal-footer button:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            padding: 8px 14px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
        }

        /* Edit Button Style */
        .edit-btn {
            padding: 8px 14px;
            background-color: #28a745;
            color: white;
            border-radius: 6px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-btn:hover {
            background-color: #218838;
        }

        /* Modal Content Styling */
        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Input Fields */
        .modal-content input,
        .modal-content select {
            padding: 12px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ddd;
            width: 100%;
            box-sizing: border-box
        }

        /* Time Input Specific Styling */
        .modal-content input[type="time"] {
            width: 100%;
        }

        /* Submit Button */
        .modal-content button[type="submit"] {
            background-color: #007BFF;
            color: white;
            padding: 12px;
            font-size: 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        /* Submit Button Hover */
        .modal-content button[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Label Styling */
        .modal-content label {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }

        a.active {
            font-weight: bold;
            text-decoration: underline;
            color:rgb(255, 255, 255) !important; /* Optional: yellow highlight */
        }

    </style>
</head>

<body>

    <?php $activePage = 'jv-dashboard'; ?>
    <?php include 'jv-navbar.php'; ?>



    <div class="container">
        <!-- Header Section -->
        

        <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 10px;">
            <button class="add-btn" onclick="window.location.href='jv-add_attendance.php'">Add New Attendance</button>
        </div>









        <!-- Attendance Table -->
        <table>
            <thead>
                <tr>
                    <th>Intern ID</th>
                    <th>Intern Name</th>
                    <th>Date</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendanceRecords as $record): ?>
                    <?php
                    // Fetch the intern's name based on user_id
                    $sqlUser = "SELECT username FROM users WHERE id = ?";
                    $stmtUser = $pdo->prepare($sqlUser);
                    $stmtUser->execute([$record['user_id']]);
                    $user = $stmtUser->fetch();
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($record['date']); ?></td>
                        <td><?php echo htmlspecialchars($record['check_in']); ?></td>
                        <td><?php echo htmlspecialchars($record['check_out']); ?></td>
                        <td><?php echo htmlspecialchars($record['status']); ?></td>
                        <td>
                            <!-- Edit Attendance Trigger Modal -->
                            <button class="edit-btn" onclick="openEditModal(<?php echo $record['id']; ?>, '<?php echo $record['check_in']; ?>', '<?php echo $record['check_out']; ?>', '<?php echo $record['status']; ?>')" title="Click to edit this attendance record">Edit</button>

                            <!-- Delete Attendance Trigger Modal -->
                            <button class="delete-btn" onclick="openDeleteModal(<?php echo $record['id']; ?>)" title="Delete this attendance record">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        
        
        
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <h2>Edit Attendance</h2>
            <form id="editForm" method="POST" action="jv-edit_attendance.php">
                <input type="hidden" name="id" id="editId">
                <label for="check_in">Check-in Time:</label>
                <input type="time" name="check_in" id="editCheckIn" required><br>
                <label for="check_out">Check-out Time:</label>
                <input type="time" name="check_out" id="editCheckOut" required><br>
                <label for="status">Status:</label>
                <select name="status" id="editStatus" required>
                    <option value="present">Present</option>
                    <option value="late">Late</option>
                    <option value="absent">Absent</option>
                </select><br>
                <button type="submit" class="edit-btn">Update</button>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            <h2>Are you sure you want to delete this record?</h2>
            <div class="modal-footer">
                <button onclick="deleteRecord()">Yes, Delete</button>
                <button onclick="closeModal('deleteModal')">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        // Open the edit modal and populate the fields
        function openEditModal(id, checkIn, checkOut, status) {
            document.getElementById('editId').value = id;
            document.getElementById('editCheckIn').value = checkIn;
            document.getElementById('editCheckOut').value = checkOut;
            document.getElementById('editStatus').value = status;
            document.getElementById('editModal').style.display = 'block';
        }

        // Open the delete modal
        function openDeleteModal(id) {
            document.getElementById('deleteModal').style.display = 'block';
            window.deleteId = id; // Store the ID for later deletion
        }

        // Close the modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Delete the record
        function deleteRecord() {
            // Send a GET request to delete_attendance.php with the ID
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'jv-delete_attendance.php?id=' + window.deleteId, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // On success, close the modal and reload the page to update the table
                    closeModal('deleteModal');
                    location.reload(); // Refresh the page to remove the deleted record
                } else {
                    alert('Error deleting the record.');
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
