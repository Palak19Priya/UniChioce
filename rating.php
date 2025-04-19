<?php
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['rating'])) {
            $rating_value = (int)$_POST['rating'];
            
            // Validate rating value (should be between 1 and 5)
            if ($rating_value >= 1 && $rating_value <= 5) {
                $sql = "INSERT INTO ratings (rating_value) VALUES (?)";
                
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $rating_value);

                    if ($stmt->execute()) {
                        echo json_encode(['status' => 'success', 'message' => 'Thank you for your rating!']);
                    } else {
                        throw new Exception("Execute failed: " . $stmt->error);
                    }

                    $stmt->close();
                } else {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
            } else {
                throw new Exception("Invalid rating value: " . $rating_value);
            }
        } else {
            throw new Exception("No rating provided in POST data");
        }
    } catch (Exception $e) {
        error_log("Rating submission error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error', 
            'message' => 'Failed to submit rating: ' . $e->getMessage()
        ]);
    }
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?> 