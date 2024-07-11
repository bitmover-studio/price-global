<?php
require_once "../db/db_connection.php";

function generateJsonFromQuery($query) {
    global $conn;
    // Execute the SQL query
    $result = $conn->query($query);

    // Fetch the results and store them in an array
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Set the appropriate headers to output JSON
    header('Content-Type: application/json');

    // Output the JSON data
    echo json_encode($data, JSON_PRETTY_PRINT);
}

// SQL query to retrieve data
$query = "SELECT last.symbol, last.created_at as created, last.last, volume, exchange_name as exchange FROM price_last last";

// Call the function to output the JSON data
generateJsonFromQuery($query);