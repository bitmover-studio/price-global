<?php
require_once "exchanges.php";
require_once "getapidata.php";

function StartPoolingRates() {
    $fiat_rates = GetRates();

    fetchRatesFromEndpoints($fiat_rates);
}
StartPoolingRates();