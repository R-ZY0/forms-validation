<?php
/**
 * Database Configuration
 * Enhanced with error handling and environment variables
 */

// Database credentials - use environment variables in production
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'blog');

// Create connection
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$con) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Sorry, we're experiencing technical difficulties. Please try again later.");
}

// Set charset to UTF-8
if (!mysqli_set_charset($con, "utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . mysqli_error($con));
}

// Set timezone
mysqli_query($con, "SET time_zone = '+00:00'");

/**
 * Helper function to safely close database connection
 */
function closeConnection() {
    global $con;
    if ($con) {
        mysqli_close($con);
    }
}

// Register shutdown function
register_shutdown_function('closeConnection');
?>