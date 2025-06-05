<?php
// Connect to MySQL (XAMPP)
$host = "localhost";
$username = "root";
$password = "";
$dbname = "intern_eval"; // ← change this to your actual DB name

$conn = new mysqli($host, $username, $password, $dbname, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $intern_id = $_POST['intern_id'];
    $total_score = $_POST['total_score'];
    $behavior_score = $_POST['behavior_score'];

    // Check if intern already exists
    $check_sql = "SELECT * FROM hiring_evaluations WHERE intern_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $intern_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing record
        $update_sql = "UPDATE hiring_evaluations SET total_score=?, behavior_score=? WHERE intern_id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("dds", $total_score, $behavior_score, $intern_id);
        $update_stmt->execute();
        $msg = "Updated evaluation for Intern ID: $intern_id";
    } else {
        // Insert new record
        $insert_sql = "INSERT INTO hiring_evaluations (intern_id, total_score, behavior_score) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sdd", $intern_id, $total_score, $behavior_score);
        $insert_stmt->execute();
        $msg = "Inserted new evaluation for Intern ID: $intern_id";
    }
}

// Fetch evaluations
$sql = "SELECT * FROM hiring_evaluations ORDER BY last_updated DESC";
$records = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Intern Hiring Recommendation System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
        }
        
        .header {
            background-color: #0052FF;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 500;
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .form-title {
            font-size: 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            color: #333;
        }
        
        .form-title svg {
            margin-right: 10px;
            color: #FF4D4D;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #0052FF;
            outline: none;
        }
        
        .btn {
            background-color: #0052FF;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #0043CC;
        }
        
        .btn-success {
            background-color: #2ECC71;
        }
        
        .btn-success:hover {
            background-color: #27AE60;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background-color: #0052FF;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 500;
        }
        
        .table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .table tr:hover {
            background-color: #f9f9f9;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: #D5F5E3;
            color: #27AE60;
        }
        
        .badge-warning {
            background-color: #FCF3CF;
            color: #F39C12;
        }
        
        .badge-danger {
            background-color: #FADBD8;
            color: #E74C3C;
        }
        
        .view-btn {
            background-color: #0052FF;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }
        
        .success-alert {
            background-color: #D5F5E3;
            color: #27AE60;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .success-alert svg {
            margin-right: 10px;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            background-color: #2ECC71;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .back-btn svg {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Interns Evaluation System</h1>
</div>

<div class="container">
    <a href="index.php" class="back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
        </svg>
        Back
    </a>
    
    <?php if (isset($msg)): ?>
    <div class="success-alert">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
        </svg>
        <?= $msg ?>
    </div>
    <?php endif; ?>
    
    <div class="card">
        <h2 class="form-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
            </svg>
            Automated Hiring Recommendation
        </h2>
        
        <form method="POST">
            <div class="form-group">
                <input type="text" class="form-control" name="intern_id" placeholder="Intern ID" required>
            </div>
            <div class="form-group">
                <input type="number" step="0.01" class="form-control" name="total_score" placeholder="Total Performance Score (TPS)" required>
            </div>
            <div class="form-group">
                <input type="number" step="0.01" class="form-control" name="behavior_score" placeholder="Behavior Score (FBS)" required>
            </div>
            <button type="submit" class="btn btn-success">Submit Evaluation</button>
        </form>
    </div>
    
    <div class="card">
        <h2 class="form-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 9.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V11.5z"/>
            </svg>
            Intern Hiring Evaluations
        </h2>
        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Intern ID</th>
                        <th>Total Score</th>
                        <th>Behavior Score</th>
                        <th>Hiring Score</th>
                        <th>Recommendation</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($records->num_rows > 0): ?>
                        <?php while ($row = $records->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['intern_id']) ?></td>
                                <td><?= htmlspecialchars($row['total_score']) ?></td>
                                <td><?= htmlspecialchars($row['behavior_score']) ?></td>
                                <td><?= htmlspecialchars($row['hiring_score']) ?></td>
                                <td>
                                    <?php 
                                    $rec = strtolower($row['recommendation']);
                                    if (strpos($rec, 'highly') !== false || strpos($rec, 'hire') !== false): ?>
                                        <span class="badge badge-success">Hire (Highly Recommended)</span>
                                    <?php elseif (strpos($rec, 'consider') !== false): ?>
                                        <span class="badge badge-warning">Consider</span>
                                    <?php elseif (strpos($rec, 'not') !== false): ?>
                                        <span class="badge badge-danger">Not Recommended</span>
                                    <?php else: ?>
                                        <span class="badge"><?= htmlspecialchars($row['recommendation']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['last_updated']) ?></td>
                                <td><button class="view-btn">View</button></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>