<?php
require_once "exchanges.php";
require_once "getapidata.php";

function StartInserting() {
    $exchanges = GetExchanges();
    
    fetchDataFromEndpoints($exchanges,true);
}

// Worker to update price_last
StartInserting();
