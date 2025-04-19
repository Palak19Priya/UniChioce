<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db_config.php';

// Function to output results in a nice format
function output($title, $content, $isError = false) {
    echo "<div style='" . ($isError ? "color:red;" : "") . "margin:10px 0;'>";
    echo "<h3>$title</h3>";
    
    if (is_array($content)) {
        echo "<pre>" . print_r($content, true) . "</pre>";
    } else {
        echo "<p>$content</p>";
    }
    
    echo "</div>";
}

// Check if a username was provided
$username = $_GET['username'] ?? ($_SESSION['username'] ?? null);

if (!$username) {
    output("Error", "No username provided. Please provide a username parameter or login first.", true);
    echo "<p>Current session ID: " . session_id() . "</p>";
    echo "<p>Current session data: </p><pre>" . print_r($_SESSION, true) . "</pre>";
    exit;
}

try {
    // Connect to database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    output("Database Connection", "Successfully connected to the database");
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        output("User Check", "User '$username' exists in the database:", false);
        // Mask sensitive data
        $user['password'] = '********';
        output("User Data", $user);
        
        // Check for existing searches
        $stmt = $conn->prepare("SELECT COUNT(*) FROM user_searches WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $searchCount = $stmt->fetchColumn();
        
        output("Search History", "User has $searchCount saved searches");
        
        // Insert a test search record
        try {
            $conn->beginTransaction();
            
            $testData = [
                ':username' => $username,
                ':course' => 'Test Course',
                ':major' => 'Test Major',
                ':institution_type' => 'University',
                ':mode_of_study' => 'Full-time',
                ':country' => 'India',
                ':region' => 'Test Region',
                ':campus_setting' => 'Urban',
                ':tenth_percentage' => 80.5,
                ':twelfth_percentage' => 85.0,
                ':exam_scores' => 'Test Scores',
                ':admission_preference' => 'direct',
                ':sports' => 1,
                ':painting' => 0,
                ':dance_music' => 0,
                ':other_activities' => 'None',
                ':budget_range' => 'medium',
                ':scholarship' => 1,
                ':education_loan' => 0
            ];
            
            $stmt = $conn->prepare("INSERT INTO user_searches (
                username, course, major, institution_type, mode_of_study, 
                country, region, campus_setting, tenth_percentage, twelfth_percentage,
                exam_scores, admission_preference, sports, painting, dance_music,
                other_activities, budget_range, scholarship, education_loan
            ) VALUES (
                :username, :course, :major, :institution_type, :mode_of_study,
                :country, :region, :campus_setting, :tenth_percentage, :twelfth_percentage,
                :exam_scores, :admission_preference, :sports, :painting, :dance_music,
                :other_activities, :budget_range, :scholarship, :education_loan
            )");
            
            $stmt->execute($testData);
            $id = $conn->lastInsertId();
            $conn->commit();
            
            output("Test Insert", "Successfully inserted a test search with ID: $id");
            
            // Verify the inserted record
            $stmt = $conn->prepare("SELECT * FROM user_searches WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $search = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($search) {
                output("Inserted Data", $search);
            }
            
        } catch (PDOException $e) {
            $conn->rollBack();
            output("Test Insert Error", "Failed to insert test search: " . $e->getMessage(), true);
            
            // Check for foreign key constraint issues
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                output("Foreign Key Error", "This appears to be a foreign key constraint issue. Make sure the username exists in the users table.", true);
                
                // Show users table structure
                $stmt = $conn->query("DESCRIBE users");
                $usersStructure = $stmt->fetchAll(PDO::FETCH_ASSOC);
                output("Users Table Structure", $usersStructure);
                
                // Show user_searches table structure
                $stmt = $conn->query("DESCRIBE user_searches");
                $searchesStructure = $stmt->fetchAll(PDO::FETCH_ASSOC);
                output("User Searches Table Structure", $searchesStructure);
            }
        }
        
    } else {
        output("User Check", "User '$username' does not exist in the database", true);
        
        // List some users from the database
        $stmt = $conn->query("SELECT username FROM users LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($users) > 0) {
            output("Available Users", "Here are some existing users in the database: " . implode(", ", $users));
        } else {
            output("No Users", "There are no users in the database.", true);
        }
    }
    
} catch (PDOException $e) {
    output("Database Error", "Failed to connect to the database: " . $e->getMessage(), true);
}

// Add a form to try another username
echo "<hr><h3>Try another username:</h3>";
echo "<form method='get'>";
echo "<input type='text' name='username' placeholder='Enter username'>";
echo "<button type='submit'>Check</button>";
echo "</form>";

// Add a link to go back to college finder
echo "<p><a href='college_finder.html'>Back to College Finder</a></p>";
?> 