<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $feedbackType = $_POST['feedbackType'];
    $comments = $_POST['comments'];
    $rating = $_POST['rating'] ?? null;

    // Store feedback in the database
    $sql = "INSERT INTO feedback (name, email, feedback_type, comments, rating, submission_date) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $feedbackType, $comments, $rating);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Thank you for your feedback!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit feedback']);
    }

    $stmt->close();
    $conn->close();
}
?> 