<?php
// Database connection details
$servername = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');

// Create a new MySQL connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select the database
if (!$conn->select_db($database)) {
    die("Database selection failed: " . $conn->error);
}
