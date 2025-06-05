<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Attendance System</h2>
    <input type="text" id="name" placeholder="Enter your name">
    <button class="btn checkin-btn" onclick="markAttendance('checkin')">Check In</button>
    <button class="btn checkout-btn" onclick="markAttendance('checkout')">Check Out</button>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Check-In Time</th>
                <th>Check-Out Time</th>
            </tr>
        </thead>
        <tbody id="attendanceList">
            <?php foreach ($attendanceData as $name => $record): ?>
                <tr>
                    <td><?= htmlspecialchars($name) ?></td>
                    <td><?= htmlspecialchars($record['checkin']) ?></td>
                    <td><?= htmlspecialchars($record['checkout']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function markAttendance(type) {
            let name = document.getElementById("name").value;
            if (name === "") {
                alert("Please enter your name");
                return;
            }
            
            let formData = new FormData();
            formData.append("name", name);
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