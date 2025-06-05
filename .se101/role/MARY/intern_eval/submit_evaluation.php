<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $intern_id = $_POST['intern_id'];
    $name = $_POST['intern_name'];
    $total_score = $_POST['total_score'];
    $behavior_score = $_POST['behavior_score'];

    // Hiring Score Calculation
    $hiring_score = ($total_score * 0.6) + ($behavior_score * 0.4);
    
    // Determine Recommendation
    if ($hiring_score >= 85) {
        $recommendation = "✅ Hire (Highly Recommended)";
    } elseif ($hiring_score >= 70) {
        $recommendation = "⚖ Consider (Conditional)";
    } else {
        $recommendation = "❌ Do Not Hire";
    }

    // Insert Data into MySQL
    $stmt = $conn->prepare("INSERT INTO hiring_evaluations (intern_id, name, total_score, behavior_score, hiring_score, recommendation) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isddds", $intern_id, $name, $total_score, $behavior_score, $hiring_score, $recommendation);
    
    if ($stmt->execute()) {
        echo "Evaluation submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>
