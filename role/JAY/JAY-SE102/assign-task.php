<?php
// Initialize variables
$message = '';

// Database connection and form handling
$conn = new mysqli('localhost', 'root', '', 'task_management');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form data after POST submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assign variables from the form
    $assignedBy = $_POST['assign_to'] ?? '';  // This is the intern the task is assigned to
    $company = $_POST['company'] ?? '';
    $taskName = $_POST['taskName'] ?? '';
    $status = $_POST['status'] ?? '';
    $deadline = $_POST['deadline'] ?? '';

    // Set created_on as current date
    $createdOn = date('m-d-Y'); // Correct format for date (YYYY-MM-DD)

    // Insert task data into the database
    $sql = "INSERT INTO tasks (assigned_by, company_name, task_name, created_on, status, deadline, assignee) 
            VALUES ('$assignedBy', '$company', '$taskName', '$createdOn', '$status', '$deadline', '$assignedBy')"; // Assigning to intern

    if ($conn->query($sql) === TRUE) {
        $message = "Task assigned successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

$assignees = ["Intern_1", "Intern_2", "Intern_3"];
$companies = ["TCU", "Faculty", "Registrar"];
$deadline = !empty($_POST['deadline']) ? date('m-d-y', strtotime($_POST['deadline'])) : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Assignment</title>
    <link rel="stylesheet" href="static/assign-task.css">
</head>
<body>

<div class="modal-container">
    <div class="modal-header">
        <h2>Assign Task</h2>
        <button class="close-btn" id="closeBtn">&times;</button>
    </div>

    <div class="modal-body">
        <?php if (!empty($message)): ?>
        <div class="success-message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <!-- Task Assignment Form -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="assign_to">Task Assign To</label>
                <select name="assign_to" id="assign_to" class="form-control"> <!-- This now assigns task to intern -->
                    <option value="">Select</option>
                    <?php foreach ($assignees as $assignee): ?>
                    <option value="<?php echo $assignee; ?>"><?php echo $assignee; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="company">Company Name</label>
                <select name="company" id="company" class="form-control">
                    <option value="">Select</option>
                    <?php foreach ($companies as $company): ?>
                    <option value="<?php echo $company; ?>"><?php echo $company; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="taskName">Task name</label>
                <input type="text" name="taskName" id="taskName" class="form-control" placeholder="Enter task">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" name="status" id="status" class="form-control" value="Pending" readonly>
            </div>

            <div class="form-group">
                <label for="allocationDate">Allocation Date</label>
                <input type="text" name="allocationDate" id="allocationDate" class="form-control" value="<?php echo date('m-d-y'); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="deadline">Deadline For Task</label>
                <div class="date-input">
                    <input type="text" name="deadline" id="deadline" class="form-control" placeholder="mm-dd-yyyy">
                    <span class="calendar-icon">📅</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-cancel" id="closeBtn">Close</button>
                <button type="submit" class="btn btn-submit">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll("#closeBtn, .btn-cancel").forEach(button => {
    button.addEventListener("click", function() {
        window.location.href = "supervisor-view.php";  
    });
});

</script>

</body>
</html>
