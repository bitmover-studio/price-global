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
        $row['last'] = (float)$row['last'];
        $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
        $row['fiat_rate'] = (float)$row['fiat_rate'];
        $row['create_at_fiat'] = date('Y-m-d H:i:s', strtotime($row['create_at_fiat']));
        $data[] = $row;
    }

    // Set the appropriate headers to output JSON
    header('Content-Type: application/json');

    // Output the JSON data
    echo json_encode($data[0], JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
}

// Get the currency parameter from the URL, default to 'USD' if not set
$currency = isset($_GET['currency']) ? $_GET['currency'] : 'USD';

// SQL query to retrieve data
if ($currency === 'USD') {
    $query = "SELECT
        pl.created_at, pl.last, pl.volume, pl.exchange_name,
        NOW() as create_at_fiat, 'USD' as fiat, 1 as fiat_rate, 'Central Bank' as source
    FROM
        `price_last` pl
    WHERE
        pl.symbol = 'USD';";
} else {
    $query = "SELECT
        pl.created_at, pl.last, pl.volume, pl.exchange_name,
        fr.create_at_fiat, fr.fiat, fr.fiat_rate, fr.source
    FROM
        `price_last` pl
    JOIN (
        SELECT created_at as create_at_fiat, fiat, last as fiat_rate, source
        FROM `fiat_rates`
        WHERE fiat = '$currency'
        ORDER BY created_at DESC
        LIMIT 1
    ) fr ON pl.symbol = fr.fiat
    WHERE pl.symbol = '$currency';";
}

// Call the function to output the JSON data
generateJsonFromQuery($query);
