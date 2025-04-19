<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $university_query = $_POST['university_query'];
    $budget = $_POST['budget'];
    $location = $_POST['location'];

    // Store the search in the database
    $sql = "INSERT INTO incognito_searches (university_query, budget, location, search_date) 
            VALUES (?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $university_query, $budget, $location);

    if ($stmt->execute()) {
        // Here you would typically perform the actual search and return results
        // For now, we'll just return a success message
        echo json_encode(['status' => 'success', 'message' => 'Search recorded successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Search failed']);
    }

    $stmt->close();
    $conn->close();
}
?> 