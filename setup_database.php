<?php
// Enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database configuration
require_once 'db_config.php';

// Function to execute SQL and handle errors
function executeSQL($conn, $sql, $description) {
    try {
        $conn->exec($sql);
        echo "<div style='color:green;margin:5px 0;'>✓ $description successful</div>";
        return true;
    } catch (PDOException $e) {
        echo "<div style='color:red;margin:5px 0;'>✗ $description failed: " . $e->getMessage() . "</div>";
        return false;
    }
}

echo "<h1>UniChoice Database Setup</h1>";

try {
    // Connect to MySQL server without selecting a database
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div style='color:green'>Connected to MySQL server successfully!</div>";
    
    // Create the database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    executeSQL($conn, $sql, "Database creation");
    
    // Select the database
    $conn->exec("USE $dbname");
    echo "<div style='color:green'>Using database: $dbname</div>";
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(100) NOT NULL,
        dob DATE NOT NULL,
        gender VARCHAR(20) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(15) NOT NULL,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    executeSQL($conn, $sql, "Users table creation");
    
    // Create incognito_searches table
    $sql = "CREATE TABLE IF NOT EXISTS incognito_searches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        university_query VARCHAR(255) NOT NULL,
        budget VARCHAR(50) NOT NULL,
        location VARCHAR(100) NOT NULL,
        search_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    executeSQL($conn, $sql, "Incognito searches table creation");
    
    // Create feedback table
    $sql = "CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(100),
        feedback_type VARCHAR(50) NOT NULL,
        comments TEXT NOT NULL,
        rating INT,
        submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    executeSQL($conn, $sql, "Feedback table creation");
    
    // Create ratings table
    $sql = "CREATE TABLE IF NOT EXISTS ratings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rating_value INT NOT NULL,
        submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    executeSQL($conn, $sql, "Ratings table creation");
    
    // Create user_searches table
    $sql = "CREATE TABLE IF NOT EXISTS user_searches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        course VARCHAR(50) NOT NULL,
        major VARCHAR(100) NOT NULL,
        institution_type VARCHAR(50) NOT NULL,
        mode_of_study VARCHAR(50) NOT NULL,
        country VARCHAR(50) NOT NULL,
        region VARCHAR(100) NOT NULL,
        campus_setting VARCHAR(50) NOT NULL,
        tenth_percentage DECIMAL(5,2) NOT NULL,
        twelfth_percentage DECIMAL(5,2) NOT NULL,
        exam_scores TEXT,
        admission_preference VARCHAR(50) NOT NULL,
        sports BOOLEAN DEFAULT FALSE,
        painting BOOLEAN DEFAULT FALSE,
        dance_music BOOLEAN DEFAULT FALSE,
        other_activities TEXT,
        budget_range VARCHAR(50) NOT NULL,
        scholarship BOOLEAN NOT NULL,
        education_loan BOOLEAN NOT NULL,
        search_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (username) REFERENCES users(username)
    )";
    executeSQL($conn, $sql, "User searches table creation");
    
    // Create colleges table
    $sql = "CREATE TABLE IF NOT EXISTS colleges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        type VARCHAR(50) NOT NULL,
        country VARCHAR(50) NOT NULL,
        region VARCHAR(100) NOT NULL,
        campus_setting VARCHAR(50) NOT NULL,
        courses_offered TEXT NOT NULL,
        majors_available TEXT NOT NULL,
        mode_of_study VARCHAR(50) NOT NULL,
        min_tenth_percentage DECIMAL(5,2),
        min_twelfth_percentage DECIMAL(5,2),
        accepts_exam_scores BOOLEAN DEFAULT TRUE,
        admission_type VARCHAR(50) NOT NULL,
        has_sports_facilities BOOLEAN DEFAULT FALSE,
        has_arts_facilities BOOLEAN DEFAULT FALSE,
        has_performing_arts BOOLEAN DEFAULT FALSE,
        annual_fees DECIMAL(10,2) NOT NULL,
        offers_scholarships BOOLEAN DEFAULT FALSE,
        has_loan_facility BOOLEAN DEFAULT FALSE,
        rating DECIMAL(3,1),
        website VARCHAR(255),
        contact_email VARCHAR(100),
        contact_phone VARCHAR(20)
    )";
    executeSQL($conn, $sql, "Colleges table creation");
    
    // Insert a test user
    $userCheck = $conn->query("SELECT COUNT(*) FROM users WHERE username = 'test_user'")->fetchColumn();
    
    if ($userCheck == 0) {
        $sql = "INSERT INTO users (fullname, dob, gender, email, phone, username, password) 
                VALUES ('Test User', '2000-01-01', 'Other', 'test@example.com', '1234567890', 'test_user', '" . 
                password_hash('password123', PASSWORD_DEFAULT) . "')";
        executeSQL($conn, $sql, "Test user creation");
    } else {
        echo "<div style='color:blue'>Test user already exists</div>";
    }
    
    // Check if colleges table has data
    $collegeCount = $conn->query("SELECT COUNT(*) FROM colleges")->fetchColumn();
    
    if ($collegeCount == 0) {
        // Insert sample college data
        $sql = "INSERT INTO colleges (name, type, country, region, campus_setting, courses_offered, 
                majors_available, mode_of_study, min_tenth_percentage, min_twelfth_percentage, 
                admission_type, has_sports_facilities, has_arts_facilities, has_performing_arts, 
                annual_fees, offers_scholarships, has_loan_facility, rating, website, contact_email, contact_phone) VALUES
            ('Indian Institute of Technology, Bombay', 'Autonomous', 'India', 'Maharashtra', 'Urban', 'B.Tech,M.Tech,B.Sc,M.Sc', 'Computer Science,Electrical Engineering,Mechanical Engineering', 'Full-time', 90.00, 85.00, 'counseling', TRUE, TRUE, TRUE, 250000.00, TRUE, TRUE, 4.8, 'https://www.iitb.ac.in', 'info@iitb.ac.in', '+912225762222'),
            ('Delhi University', 'University', 'India', 'Delhi', 'Urban', 'BA,B.Sc,B.Com,MA,M.Sc', 'Computer Science,Psychology,English Literature', 'Full-time', 75.00, 70.00, 'direct', TRUE, TRUE, TRUE, 150000.00, TRUE, TRUE, 4.5, 'https://www.du.ac.in', 'info@du.ac.in', '+911127666687'),
            ('Manipal Institute of Technology', 'Deemed', 'India', 'Karnataka', 'Urban', 'B.Tech,M.Tech,B.Sc', 'Computer Science,Electronics,Biotechnology', 'Full-time', 80.00, 75.00, 'direct', TRUE, TRUE, TRUE, 350000.00, TRUE, TRUE, 4.6, 'https://manipal.edu', 'info@manipal.edu', '+918202571111')";
        executeSQL($conn, $sql, "Sample colleges data insertion");
    } else {
        echo "<div style='color:blue'>College data already exists ($collegeCount colleges found)</div>";
    }
    
    echo "<h2>Database Setup Completed Successfully!</h2>";
    echo "<p>You can now begin using the application.</p>";
    echo "<a href='college_finder.html'>Return to College Finder</a>";
    
} catch(PDOException $e) {
    echo "<h2 style='color:red'>Database Setup Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?> 