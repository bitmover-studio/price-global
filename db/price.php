<?php
require_once "db_connection.php";

// Function to insert data into the exchanges table
function createExchange($name)
{
    global $conn;
    $sql = "INSERT INTO exchanges (name) VALUES (?) ON DUPLICATE KEY UPDATE name = VALUES(name);";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $name);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    return $stmt->affected_rows > 0;
}

// Function to insert data into the exchanges table
function createSymbol($name)
{
    global $conn;
    $sql = "INSERT INTO symbols (symbol) VALUES (?) ON DUPLICATE KEY UPDATE symbol = VALUES(symbol);";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $name);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    return $stmt->affected_rows > 0;
}

function UpdateLast($last, $volume, $quote_volume, $symbol, $exchange)
{
    global $conn;
    $insertQuery = "INSERT INTO price_last
        (last, volume, quote_volume, symbol, exchange_name)
    VALUES
        ('$last', '$volume', '$quote_volume', '$symbol', '$exchange')
        ON DUPLICATE KEY UPDATE
            last = VALUES(last),
            volume = VALUES(volume),
            quote_volume = VALUES(quote_volume),
            created_at = NOW()";

    if ($conn->query($insertQuery) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}