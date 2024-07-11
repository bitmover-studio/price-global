<?php
require_once "./db/db_connection.php";
require_once "exchanges.php";
require_once "getapidata.php";

function StartPoolingLoop() {
    $exchanges = GetExchanges();
    
    fetchDataFromEndpoints($exchanges);
}

// Worker to update price_last
StartPoolingLoop();
