<?php
require_once(__DIR__ . "/db/db_connection.php");
require_once(__DIR__ . "/db/create_tables.php");

$table_creation_queries = [
    $create_exchanges_table,
    $create_symbols_table,
    $create_pricelast_table,
    $create_fiat_rates_table
];

// Function to create tables
function create_tables($conn, $queries)
{
    foreach ($queries as $query) {
        if ($conn->query($query) === TRUE) {
            echo "Table created successfully: " . $query . "<br>";
        } else {
            echo "Error creating table: " . $conn->error . "<br>";
        }
    }
}

create_tables($conn, $table_creation_queries);

// Execute the query to add indexes
try {
    $conn->query($create_index);
} catch (mysqli_sql_exception $ex) {
    if ($ex->getCode() == 1061) {
        echo 'Index already exists';
    } else {
        echo 'Error adding index: ' . $ex->getMessage();
    }
}
