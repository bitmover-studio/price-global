<?php
require_once "../../db/db_connection.php";

function generateJsonFromQuery($query)
{
    global $conn;
    // Execute the SQL query
    $result = $conn->query($query);

    // Fetch the results and store them in an array
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $row['Last'] = (float)$row['Last'];
        $row['Created'] = date('Y-m-d H:i:s', strtotime($row['Created']));
        $data[] = $row;
    }

    // Set the appropriate headers to output JSON
    header('Content-Type: application/json');

    // Output the JSON data
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
}

// SQL query to retrieve data
$query = "SELECT
    fiat as Fiat,
    last as Last,
    source as Source,
    created_at AS Created
FROM
    (
        SELECT
            *,
            ROW_NUMBER() OVER (PARTITION BY fiat, source ORDER BY created_at DESC) AS rn
        FROM
            fiat_rates
        WHERE
            created_at >= DATE_SUB(NOW(), INTERVAL 12 HOUR)
    ) AS ranked_fiat_rates
WHERE
    rn = 1;";

// Call the function to output the JSON data
generateJsonFromQuery($query);
