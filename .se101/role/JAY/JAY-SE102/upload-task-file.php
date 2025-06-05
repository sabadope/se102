<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && isset($_POST['task_id'])) {
        $taskId = intval($_POST['task_id']);
        $file = $_FILES['file'];

        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // create uploads dir if not exists
        }

        $fileName = basename($file["name"]);
        $targetFilePath = $targetDir . time() . "_" . $fileName;

        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            // Update task record with file path & mark as completed
           // Assume $targetFilePath is the location where the file was uploaded
$stmt = $conn->prepare("UPDATE tasks SET status='completed', file_path=? WHERE id=?");
$stmt->bind_param("si", $targetFilePath, $taskId);


            if ($stmt->execute()) {
                echo "File uploaded and task marked as completed.";
            } else {
                echo "Database update failed.";
            }

            $stmt->close();
        } else {
            echo "Failed to upload file.";
        }
    } else {
        echo "No file or task ID provided.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
