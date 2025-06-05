<?php
session_start();
$dataFile = 'attendance.json';

// Load existing data
if (file_exists($dataFile)) {
    $attendanceData = json_decode(file_get_contents($dataFile), true);
} else {
    $attendanceData = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $intern_id = $_POST['intern_id'];
    $type = $_POST['type'];
    $time = date('H:i:s');
    
    if (!isset($attendanceData[$id])) {
        $attendanceData[$id] = [
            'intern_id' => $intern_id,
            'checkin' => '',
            'checkout' => '',
            'status' => 'Absent',
            'total_hours' => ''
        ];
    }
    
    if ($type === 'checkin' && empty($attendanceData[$id]['checkin'])) {
        $attendanceData[$id]['checkin'] = $time;
        $attendanceData[$id]['status'] = 'Present';
    } elseif ($type === 'checkout' && empty($attendanceData[$id]['checkout'])) {
        $attendanceData[$id]['checkout'] = $time;
        
        // Calculate total hours
        $checkinTime = strtotime($attendanceData[$id]['checkin']);
        $checkoutTime = strtotime($time);
        $totalHours = round(($checkoutTime - $checkinTime) / 3600, 2);
        $attendanceData[$id]['total_hours'] = $totalHours . ' hours';
    } else {
        echo json_encode(['error' => 'Already checked in/out']);
        exit;
    }
    
    file_put_contents($dataFile, json_encode($attendanceData));
    echo json_encode($attendanceData);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Attendance System</h2>
    <input type="text" id="id" placeholder="Enter ID">
    <input type="text" id="intern_id" placeholder="Enter Intern ID">
    <button class="btn checkin-btn" onclick="markAttendance('checkin')">Check In</button>
    <button class="btn checkout-btn" onclick="markAttendance('checkout')">Check Out</button>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Intern ID</th>
                <th>Check-In Time</th>
                <th>Check-Out Time</th>
                <th>Status</th>
                <th>Total Hours</th>
            </tr>
        </thead>
        <tbody id="attendanceList">
            <?php foreach ($attendanceData as $id => $record): ?>
                <tr>
                    <td><?= htmlspecialchars($id) ?></td>
                    <td><?= htmlspecialchars($record['intern_id']) ?></td>
                    <td><?= htmlspecialchars($record['checkin']) ?></td>
                    <td><?= htmlspecialchars($record['checkout']) ?></td>
                    <td><?= htmlspecialchars($record['status']) ?></td>
                    <td><?= htmlspecialchars($record['total_hours']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function markAttendance(type) {
            let id = document.getElementById("id").value;
            let intern_id = document.getElementById("intern_id").value;
            
            if (id === "" || intern_id === "") {
                alert("Please enter all required fields");
                return;
            }
            
            let formData = new FormData();
            formData.append("id", id);
            formData.append("intern_id", intern_id);
            formData.append("type", type);
            
            fetch("", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
