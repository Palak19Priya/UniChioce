<?php
header('Content-Type: application/json');

// Database credentials
$host = 'localhost';
$user = 'root';
$password = ''; // change if needed
$dbname = 'unichoice_db'; // replace with your DB name

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Get POST data
$university_query = $_POST['university_query'];
$budget = $_POST['budget'];
$location = $_POST['location'];

// Sanitize input (basic)
$university_query = $conn->real_escape_string($university_query);
$budget = (int)$budget;
$location = $conn->real_escape_string($location);

// Store the search in incognito_searches table
$stmt = $conn->prepare("INSERT INTO incognito_searches (university_query, budget, location) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $university_query, $budget, $location);
$stmt->execute();
$stmt->close();

// Search matching universities from universities table
$query = "SELECT name, location, min_budget, max_budget FROM universities 
          WHERE name LIKE CONCAT('%', ?, '%') 
          AND budget_range <= ? 
          AND location LIKE CONCAT('%', ?, '%')";

$stmt = $conn->prepare($query);
$stmt->bind_param("sis", $university_query, $budget, $location);
$stmt->execute();
$result = $stmt->get_result();

$universities = [];
while ($row = $result->fetch_assoc()) {
    $universities[] = $row;
}
$stmt->close();
$conn->close();


if (count($universities) > 0) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Universities found',
        'data' => $universities
    ]);
} else {
    echo json_encode([
        'status' => 'fail',
        'message' => 'No universities matched your search.'
    ]);
}
?>
