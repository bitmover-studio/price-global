<?php
require_once "../../db/db_connection.php";

function generateJsonFromQuery($query)
{
    global $conn;
    // Execute the SQL query
    $result = $conn->query($query);

    if (!$result) {
        // Handle query error
        http_response_code(500);
        echo json_encode(["error" => "Database query failed"]);
        exit;
    }

    // Fetch the results and store them in an array
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $row['last'] = (float)$row['last'];
        $row['fiat_rate'] = (float)$row['fiat_rate'];
        $row['created_at'] = (int)$row['created_at'];
        $row['create_at_fiat'] = (int)$row['create_at_fiat'];
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
         UNIX_TIMESTAMP(pl.created_at) AS created_at, pl.last, pl.volume, pl.exchange_name,
        UNIX_TIMESTAMP(NOW()) as create_at_fiat, 'USD' as fiat, 1 as fiat_rate, 'Central Bank' as source
    FROM
        `price_last` pl
    WHERE
        pl.symbol = 'USD';";
} else {
    $query = "SELECT
        UNIX_TIMESTAMP(pl.created_at) AS created_at,
        pl.last,
        pl.volume,
        pl.exchange_name,
        UNIX_TIMESTAMP(fr.create_at_fiat) AS create_at_fiat,
        fr.fiat,
        fr.fiat_rate,
        fr.source
    FROM
        `price_last` pl
    JOIN (
        SELECT created_at AS create_at_fiat, fiat, last AS fiat_rate, source
        FROM `fiat_rates`
        WHERE fiat = '$currency'
        ORDER BY created_at DESC
        LIMIT 1
    ) fr ON pl.symbol = fr.fiat
    WHERE pl.symbol = '$currency';";
}

// Call the function to output the JSON data
generateJsonFromQuery($query);
