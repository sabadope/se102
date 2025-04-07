<!-- Intern Task Dashboard -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - Intern View</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="static/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Add jQuery -->
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar min-vh-100 bg-dark text-white p-3">
                <a href="intern-view.php" class="d-block text-white py-2">Task Management</a>
                <a href="view-task-intern.php" class="d-block text-white py-2">View Task</a>
                <a href="login.php" class="d-block text-danger py-2">Log out</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Intern Task Dashboard</h2>
                </div>

                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="card bg-warning text-dark p-3">
                            <h3>Pending</h3>
                            <div class="fs-2 fw-bold" id="pending-count"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-dark p-3">
                            <h3>In-progress</h3>
                            <div class="fs-2 fw-bold" id="in-progress-count"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white p-3">
                            <h3>Completed</h3>
                            <div class="fs-2 fw-bold" id="completed-count">0</div>
                        </div>
                    </div>
                </div>

                <div class="task-report mt-4">
                    <h3>Your Assigned Tasks</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="task-table">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr.no.</th>
                                <th>Task Name</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                <th>Feedback</th>
                                <th>Rating</th>
                                <th>Upload File</th>
                                <th>Submit</th>
                            </tr>
                        </thead>

                        <tbody id="task-body">
                            <!-- Tasks will be populated by AJAX -->
                        </tbody>
                    </table>
                    </div>
                    <button class="btn btn-success mt-3" id="submit-all">Submit All</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fetch tasks and update the table
        function fetchTasks() {
            $.ajax({
                url: 'fetch-intern-tasks.php',
                method: 'GET',
                success: function(data) {
                    try {
                        const tasks = JSON.parse(data);
                        let taskRows = '';

                        if (tasks.length === 0) {
                            taskRows = '<tr><td colspan="8" class="text-center">No tasks assigned.</td></tr>';
                        } else {
                            let pendingCount = 0, inProgressCount = 0, completedCount = 0;

                            tasks.forEach(task => {
                                if (task.status === "Pending") pendingCount++;
                                else if (task.status === "in-progress") inProgressCount++;
                                else if (task.status === "completed") completedCount++;

                                taskRows += `
                                    <tr>
                                        <td>${task.id}</td>
                                        <td>${task.task_name}</td>
                                        <td class="text-capitalize">
                                            <select class="form-select task-status" data-task-id="${task.id}">
                                                <option value="Pending" ${task.status === 'Pending' ? 'selected' : ''}>Pending</option>
                                                <option value="in-progress" ${task.status === 'in-progress' ? 'selected' : ''}>In-progress</option>
                                            </select>
                                        </td>
                                        <td>${task.deadline}</td>
                                        <td>${task.feedback ? task.feedback : 'No Feedback Yet'}</td>
                                        <td>
                                            <span>${task.rating !== null ? task.rating : 'No Rating Yet'}</span>
                                        </td>
                                        <td>
                                            <input type="file" class="form-control file-input" data-task-id="${task.id}">
                                        </td>
                                        <td>
                                            <button class="btn btn-primary submit-task" data-task-id="${task.id}">Submit</button>
                                        </td>
                                    </tr>
                                `;
                            });

                            // Update counts
                            $('#pending-count').text(pendingCount);
                            $('#in-progress-count').text(inProgressCount);
                            $('#completed-count').text(completedCount);
                        }

                        $('#task-body').html(taskRows);
                    } catch (error) {
                        console.error("Error parsing task data:", error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                }
            });
        }

        fetchTasks();
        setInterval(fetchTasks, 5000);

        // Handle task status change
        $(document).on('change', '.task-status', function() {
            let taskId = $(this).data('task-id');
            let newStatus = $(this).val();

            $.ajax({
                url: 'update-task-status.php',
                method: 'POST',
                data: { task_id: taskId, status: newStatus },
                success: function(response) {
                    alert(response);
                    fetchTasks();
                },
                error: function() {
                    alert('Failed to update task status.');
                }
            });
        });

        // Handle single task submission
        $(document).on('click', '.submit-task', function() {
            let taskId = $(this).data('task-id');
            let fileInput = $(`input[data-task-id="${taskId}"]`)[0];

            if (fileInput.files.length === 0) {
                alert('Please select a file before submitting.');
                return;
            }

            let formData = new FormData();
            formData.append('task_id', taskId);
            formData.append('file', fileInput.files[0]);

            $.ajax({
                url: 'upload-task-file.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert(response);
                    fetchTasks();
                },
                error: function() {
                    alert('File upload failed.');
                }
            });
        });

        // Handle submit all button
        $('#submit-all').click(function() {
            $('.submit-task').each(function() {
                $(this).click();
            });
        });
    </script>
</body>
</html>