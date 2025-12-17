<?php

// Database Configuration File
// Real Estate MLS System


// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // XAMPP has no password by default
define('DB_NAME', 'mls_database');


// Create db connection
// Returns mysqli db connection object
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Close database connection
function closeDBConnection($conn) {
    $conn->close();
}

// Sanitize input to prevent SQL injection
function sanitizeInput($conn, $input) {
    return $conn->real_escape_string(trim($input));
}

// Format price as currency
function formatPrice($price) {
    return '$' . number_format($price);
}
?>
