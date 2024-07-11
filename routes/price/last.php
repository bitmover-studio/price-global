<?php
require_once "../../db/db_connection.php";

function generateJsonFromQuery($query) {
    global $conn;
    // Execute the SQL query
    $result = $conn->query($query);

    // Fetch the results and store them in an array
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $row['Last'] = (float)$row['Last'];
        $row['Volume'] = (float)$row['Volume'];
        $data[] = $row;
    }

    // Set the appropriate headers to output JSON
    header('Content-Type: application/json');

    // Output the JSON data
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
}

// SQL query to retrieve data
$query = "SELECT last.symbol as Symbol, exchange_name as Exchange, last.last as Last , volume as Volume, last.created_at as Created FROM price_last last";

// Call the function to output the JSON data
generateJsonFromQuery($query);