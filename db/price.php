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
    $insertQuery = "INSERT INTO price_last (last, volume, quote_volume, symbol, exchange_name)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        last = VALUES(last),
                        volume = VALUES(volume),
                        quote_volume = VALUES(quote_volume),
                        created_at = NOW()";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("dddss", $last, $volume, $quote_volume, $symbol, $exchange);

    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}

function insertPriceHist($last, $volume, $quote_volume, $symbol, $exchange)
{
    global $conn;
    $insertQuery = "INSERT INTO price_hist (last, volume, quote_volume, symbol, exchange_name)
                    VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("dddss", $last, $volume, $quote_volume, $symbol, $exchange);

    if ($stmt->execute()) {
        echo "Record inserted successfully";
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}

function insertFiatRate($last, $fiat, $source)
{
    global $conn;
    $insertQuery = "INSERT INTO fiat_rates (last, fiat, source) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("dss", $last, $fiat, $source);

    if ($stmt->execute()) {
        echo "Rates " . $fiat . " " . $last . " Record inserted successfully";
    } else {
        echo "Error inserting record: " . $stmt->error;
    }
    $stmt->close();
}
