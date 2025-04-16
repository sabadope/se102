 <!-- Supervisor -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - Supervisor View</title>

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
                <a href="#" class="d-block text-white py-2">Task Management</a>
                <a href="assign-task.php" class="d-block text-white py-2">Assign Task</a>
                <a href="view-task.php" class="d-block text-white py-2">View Task</a>

                <a href="login.php" class="d-block text-danger py-2">Log out</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Task Management Dashboard</h2>
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
                            <div class="fs-2 fw-bold" id="completed-count"></div>
                        </div>
                    </div>
                </div>

                <div class="task-report mt-4">
                    <h3>Task Report</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="task-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sr.no.</th>
                                    <th>Assigned To</th>
                                    <th>Company Name</th>
                                    <th>Task Name</th>
                                    <th>Created on</th>
                                    <th>Start Date</th>
                                    <th>Start Time</th>
                                    <th>End Date</th>
                                    <th>End Time</th>
                                    <th>Status</th>
                                    <th>Deadline</th>
                                    <th>Rating</th>
                                    <th>Feedback</th>
                                    <th>Completion Time</th>
                                </tr>
                            </thead>
                            <tbody id="task-body">
                                <!-- Tasks will be populated by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to fetch and update tasks
        function fetchTasks() {
            $.ajax({
                url: 'fetch-tasks.php',
                method: 'GET',
                success: function(data) {
                    const tasks = JSON.parse(data);
                    let pendingCount = 0;
                    let inProgressCount = 0;
                    let completedCount = 0;
                    let taskRows = '';

                    tasks.forEach(task => {
                                if (task.status === "Pending") pendingCount++;
                                else if (task.status === "in-progress") inProgressCount++;
                                else if (task.status === "completed") completedCount++;

                        // Build task rows with rating and feedback form
                        taskRows += `
                            <tr>
                                <td>${task.id}</td>
                                <td>${task.assigned_by}</td>
                                <td>${task.company_name}</td>
                                <td>${task.task_name}</td>
                                <td>${task.created_on}</td>
                                <td>${task.start_date}</td>
                                <td>${task.start_time}</td>
                                <td>${task.end_date}</td>
                                <td>${task.end_time}</td>
                                <td class="text-capitalize">${task.status}</td>
                                <td>${task.deadline}</td>
                                <td>
                                    <form action="rate-task.php" method="POST">
                                        <input type="hidden" name="task_id" value="${task.id}">
                                        <select name="rating">
                                            <option value="1" ${task.rating == 1 ? 'selected' : ''}>1 Star</option>
                                            <option value="2" ${task.rating == 2 ? 'selected' : ''}>2 Stars</option>
                                            <option value="3" ${task.rating == 3 ? 'selected' : ''}>3 Stars</option>
                                            <option value="4" ${task.rating == 4 ? 'selected' : ''}>4 Stars</option>
                                            <option value="5" ${task.rating == 5 ? 'selected' : ''}>5 Stars</option>
                                        </select>
                                        <button type="submit">Submit Rating</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="feedback-task.php" method="POST">
                                        <input type="hidden" name="task_id" value="${task.id}">
                                        <textarea name="feedback" rows="4" cols="50">${task.feedback}</textarea>
                                        <button type="submit">Submit Feedback</button>
                                    </form>
                                </td>
                                <td>${task.completion_time} minutes</td>
                            </tr>
                        `;
                        
                    });

                    // Update counts
                    $('#pending-count').text(pendingCount);
                    $('#in-progress-count').text(inProgressCount);
                    $('#completed-count').text(completedCount);

                    // Update table
                    $('#task-body').html(taskRows);
                }
            });
        }

        // Fetch tasks initially and then every 5 seconds
        fetchTasks();
        setInterval(fetchTasks, 5000);
        
    </script>
</body>
</html>
