<?php
// Enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db_config.php';

echo "<h1>Login Test Page</h1>";

// Function to display session information
function displaySessionInfo() {
    echo "<h3>Current Session Information</h3>";
    echo "<div style='background:#f5f5f5;padding:10px;margin:10px 0;'>";
    echo "Session ID: " . session_id() . "<br>";
    
    if (isset($_SESSION['username'])) {
        echo "Logged in as: <strong>" . $_SESSION['username'] . "</strong><br>";
        
        if (isset($_SESSION['fullname'])) {
            echo "Full Name: " . $_SESSION['fullname'] . "<br>";
        }
        
        echo "Login time: " . (isset($_SESSION['login_time']) ? date('Y-m-d H:i:s', $_SESSION['login_time']) : 'Not set') . "<br>";
        echo "<a href='?action=logout' style='color:red;'>Logout</a>";
    } else {
        echo "Not logged in<br>";
    }
    
    echo "</div>";
}

// Process actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'login':
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                echo "<div style='color:red;'>Please enter both username and password.</div>";
            } else {
                try {
                    // Connect to database
                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Check if user exists
                    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
                    $stmt->execute([':username' => $username]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user) {
                        // For simplicity in this test, we'll just check if the user exists
                        // In production, you would verify the password
                        
                        // Set session variables
                        $_SESSION['username'] = $username;
                        $_SESSION['fullname'] = $user['fullname'] ?? 'Test User';
                        $_SESSION['login_time'] = time();
                        
                        echo "<div style='color:green;'>Login successful!</div>";
                    } else {
                        echo "<div style='color:red;'>User not found in database.</div>";
                    }
                } catch (PDOException $e) {
                    echo "<div style='color:red;'>Database error: " . $e->getMessage() . "</div>";
                }
            }
            break;
            
        case 'logout':
            // Clear session data
            $_SESSION = [];
            
            // Destroy the session
            session_destroy();
            
            echo "<div style='color:green;'>Logged out successfully!</div>";
            
            // Restart session for this page
            session_start();
            break;
            
        case 'test_user':
            // Set a test user session for testing purposes
            $_SESSION['username'] = 'test_user';
            $_SESSION['fullname'] = 'Test User';
            $_SESSION['login_time'] = time();
            
            echo "<div style='color:green;'>Test user session created!</div>";
            break;
    }
}

// Display current session information
displaySessionInfo();

// Login form
echo "<h3>Login Form</h3>";
echo "<form method='post' action='?action=login'>";
echo "<div style='margin:5px 0;'><label>Username: </label><input type='text' name='username' value='test_user'></div>";
echo "<div style='margin:5px 0;'><label>Password: </label><input type='password' name='password' value='password123'></div>";
echo "<div style='margin:10px 0;'><button type='submit'>Login</button></div>";
echo "</form>";

// Quick actions
echo "<h3>Quick Actions</h3>";
echo "<div style='margin:10px 0;'>";
echo "<a href='?action=test_user' style='margin-right:15px;'>Set Test User Session</a>";
echo "<a href='?action=logout' style='margin-right:15px;'>Logout</a>";
echo "<a href='college_finder.html' style='margin-right:15px;'>Go to College Finder</a>";
echo "</div>";

// Testing links
echo "<h3>Testing Links</h3>";
echo "<div style='margin:10px 0;'>";
echo "<a href='test_db.php' style='margin-right:15px;'>Test Database Connection</a>";
echo "<a href='check_user_exists.php' style='margin-right:15px;'>Check User Exists</a>";
echo "<a href='setup_database.php' style='margin-right:15px;'>Setup Database</a>";
echo "</div>";

?> 