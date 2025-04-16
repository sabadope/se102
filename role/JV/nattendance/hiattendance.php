<?php
// Sample data array to simulate a database
$attendanceData = [
    ["roll_number" => 1, "student_name" => "Chitra Singla", "course" => "SE 101", "attendance_status" => "Present", "attendance_date" => "2020-11-01", "faculty" => "Bhupender Rana"],
    ["roll_number" => 2, "student_name" => "Daniyal Farooque", "course" => "SE 101", "attendance_status" => "Present", "attendance_date" => "2020-11-01", "faculty" => "Bhupender Rana"],
    ["roll_number" => 3, "student_name" => "Rohit Kumar", "course" => "SE 201", "attendance_status" => "Absent", "attendance_date" => "2020-11-01", "faculty" => "Sneha Sharma"],
    ["roll_number" => 4, "student_name" => "abc", "course" => "SE 201", "attendance_status" => "Present", "attendance_date" => "2020-11-01", "faculty" => "Sneha Sharma"],
];

// Function to display the attendance list
function displayAttendance($data) {
    echo '<div class="container">';
    echo '<h2>Attendance List</h2>';
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>
            <th>Roll Number</th>
            <th>Student Name</th>
            <th>Course</th>
            <th>Attendance Status</th>
            <th>Attendance Date</th>
            <th>Faculty</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($data as $entry) {
        echo '<tr>';
        echo '<td>' . $entry['roll_number'] . '</td>';
        echo '<td>' . $entry['student_name'] . '</td>';
        echo '<td>' . $entry['course'] . '</td>';
        echo '<td>' . ($entry['attendance_status'] == "Present" ? '<span style="color:green;">Present</span>' : '<span style="color:red;">Absent</span>') . '</td>';
        echo '<td>' . $entry['attendance_date'] . '</td>';
        echo '<td>' . $entry['faculty'] . '</td>';
        echo '<td><button onclick="editEntry(' . $entry['roll_number'] . ')">Edit</button></td>';
        echo '<td><button onclick="deleteEntry(' . $entry['roll_number'] . ')">Delete</button></td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '<button onclick="addEntry()">Add</button>';
    echo '</div>';
}

// Function calls
displayAttendance($attendanceData);
?>

<script>
// Functions to handle edit and delete actions
function editEntry(rollNumber) {
    // Implement edit functionality
    alert('Edit entry with Roll Number: ' + rollNumber);
}

function deleteEntry(rollNumber) {
    // Implement delete functionality
    alert('Delete entry with Roll Number: ' + rollNumber);
}

function addEntry() {
    // Implement add functionality
    alert('Add a new entry');
}
</script>