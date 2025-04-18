<?php
// Database connection settings
$servername = "localhost";     // Change if your database is on a different server
$dbname = "unichoice_db";      // Your database name
$username = "root";            // Your database username (default is 'root' for XAMPP)
$password = "";                // Your database password (default is empty for XAMPP)

// Other database settings
$charset = "utf8mb4";          // Character set for database

// Maximum execution time (seconds)
ini_set('max_execution_time', 300); // 5 minutes

// Memory limit
ini_set('memory_limit', '256M');

// Error logging setting
ini_set('log_errors', 1);
error_log("Database connection requested from: " . $_SERVER['PHP_SELF']);
?> 