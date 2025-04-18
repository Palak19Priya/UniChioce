<?php
// Disable error display for production
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Test database connection
try {
    require_once 'db_config.php';
    
    echo "<h2>Testing Database Connection</h2>";
    
    // Test connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>Connection successful!</p>";
    
    // Check database tables
    $tables = ["users", "user_searches", "colleges"];
    
    echo "<h3>Testing Database Tables</h3>";
    echo "<ul>";
    
    foreach ($tables as $table) {
        $stmt = $conn->prepare("SHOW TABLES LIKE :table");
        $stmt->execute([':table' => $table]);
        
        if ($stmt->rowCount() > 0) {
            echo "<li>Table '$table' exists";
            
            // Get row count
            $countStmt = $conn->query("SELECT COUNT(*) FROM $table");
            $count = $countStmt->fetchColumn();
            echo " - Contains $count rows</li>";
        } else {
            echo "<li style='color:red'>Table '$table' does not exist!</li>";
        }
    }
    
    echo "</ul>";
    
    // Check user_searches structure
    echo "<h3>Structure of user_searches table</h3>";
    $stmt = $conn->query("DESCRIBE user_searches");
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            echo "<td>" . ($value ?? "NULL") . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // Output db_config values (without actual passwords)
    echo "<h3>Database Configuration</h3>";
    echo "<ul>";
    echo "<li>Server: $servername</li>";
    echo "<li>Database: $dbname</li>";
    echo "<li>Username: " . substr($username, 0, 1) . str_repeat('*', strlen($username)-1) . "</li>";
    echo "<li>Password: " . (empty($password) ? "<span style='color:red'>EMPTY!</span>" : str_repeat('*', strlen($password))) . "</li>";
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "<div style='color:red;font-weight:bold'>Database connection failed: " . $e->getMessage() . "</div>";
}

// Test session functionality
echo "<h3>Testing Session</h3>";
session_start();

if (isset($_SESSION['username'])) {
    echo "<p>Current user session: " . $_SESSION['username'] . "</p>";
} else {
    echo "<p style='color:orange'>Warning: No active user session found</p>";
    echo "<p>For testing purposes only, setting a temporary session username 'test_user'</p>";
    $_SESSION['username'] = 'test_user';
}

// Test a simple insert
echo "<h3>Testing Insert</h3>";
try {
    $test_data = [
        'username' => $_SESSION['username'],
        'course' => 'test_course',
        'major' => 'test_major',
        'institution_type' => 'University',
        'mode_of_study' => 'Full-time',
        'country' => 'India',
        'region' => 'Test Region',
        'campus_setting' => 'Urban',
        'tenth_percentage' => 85.5,
        'twelfth_percentage' => 90.2,
        'exam_scores' => 'Test scores',
        'admission_preference' => 'direct',
        'sports' => 1,
        'painting' => 0,
        'dance_music' => 1,
        'other_activities' => 'None',
        'budget_range' => 'medium',
        'scholarship' => 1,
        'education_loan' => 0
    ];
    
    // Create a test record
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
    
    $stmt->execute($test_data);
    
    echo "<p style='color:green'>Test insert successful! ID: {$conn->lastInsertId()}</p>";
    
    // Verify by getting the last inserted record
    $stmt = $conn->prepare("SELECT * FROM user_searches WHERE id = :id");
    $stmt->execute([':id' => $conn->lastInsertId()]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h4>Inserted Data:</h4>";
    echo "<table border='1'>";
    foreach ($result as $key => $value) {
        echo "<tr><th>$key</th><td>$value</td></tr>";
    }
    echo "</table>";
    
} catch(PDOException $e) {
    echo "<div style='color:red;font-weight:bold'>Test insert failed: " . $e->getMessage() . "</div>";
}
?> 