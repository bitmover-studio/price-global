<?php

function GetPriceFromBody($body, $exchange, $symbol)
{
    $price = array(
        "Last" => 0,
        "Volume" => 0,
        "QuoteVolume" => 0,
        "Symbol" => $symbol,
        "Exchange" => $exchange
    );

    switch ($exchange) {
        case "Binance":
            $responseObject = json_decode($body, true);
            if ($responseObject === null) {
                error_log("Unmarshal error - exchange: $exchange");
                return $price;
            }
            $modifiedResponse = array(
                "Last" => $responseObject["lastPrice"],
                "Volume" => $responseObject["volume"],
                "QuoteVolume" => $responseObject["quoteVolume"]
            );

            $price["Last"] = $modifiedResponse["Last"];
            $price["Volume"] = $modifiedResponse["Volume"];
            $price["QuoteVolume"] = $modifiedResponse["QuoteVolume"];
            break;

        case "Kraken":
            $responseObject = json_decode($body, true);
            if ($responseObject === null) {
                error_log("Unmarshal error - exchange: $exchange");
                return $price;
            }

            $currencyPair = "XXBTZ" . $symbol;
            if (isset($responseObject['result'][$currencyPair]['c'][0])) {
                $price['Last'] = (float) $responseObject['result'][$currencyPair]['c'][0];
                $price['Volume'] = (float) $responseObject['result'][$currencyPair]['v'][1];
                $avgPrice = (float) $responseObject['result'][$currencyPair]['p'][1];
                $price['QuoteVolume'] = $avgPrice * $price['Volume'];
            } else {
                $currencyPair = "XBT" . $symbol;
                $price['Last'] = (float) $responseObject['result'][$currencyPair]['c'][0];
                $price['Volume'] = (float) $responseObject['result'][$currencyPair]['v'][1];
                $avgPrice = (float) $responseObject['result'][$currencyPair]['p'][1];
                $price['QuoteVolume'] = $avgPrice * $price['Volume'];
            }
            break;

        case "Bitso":
            $responseObject = json_decode($body, true);
            if ($responseObject === null) {
                error_log("Unmarshal error - exchange: $exchange");
                return $price;
            }
            $modifiedResponse = array(
                "Last" => $responseObject["payload"]["last"],
                "Volume" => $responseObject["payload"]["volume"],
                "QuoteVolume" => $responseObject["payload"]["vwap"]
            );

            $price["Last"] = $modifiedResponse["Last"];
            $price["Volume"] = $modifiedResponse["Volume"];
            $price["QuoteVolume"] = $modifiedResponse["QuoteVolume"];
            break;

        case "Luno":
            $responseObject = json_decode($body, true);
            if ($responseObject === null) {
                error_log("Unmarshal error - exchange: $exchange");
                return $price;
            }
            $modifiedResponse = array(
                "Last" => $responseObject["last_trade"],
                "Volume" => $responseObject["rolling_24_hour_volume"],
                "QuoteVolume" => $responseObject["last_trade"] * $responseObject["rolling_24_hour_volume"]
            );

            $price["Last"] = $modifiedResponse["Last"];
            $price["Volume"] = $modifiedResponse["Volume"];
            $price["QuoteVolume"] = $modifiedResponse["QuoteVolume"];
            break;

        default:
            echo "No exchange found on switch case" . "exchange" . $exchange;
    }

    echo "GetPriceFromBody: exchange = " . $exchange . ", symbol = " . $symbol . ", priceToCreate = " . print_r($price, true);

    return $price;
}

function fetchDataFromEndpoints($exchanges)
{
    foreach ($exchanges as $exchange) {
        $symbolsAndEndpoints = $exchange["SymbolsAndEndpoints"];
        $name = $exchange["Name"];

        //Create Exchange is doesn't exists
        createExchange($name);

        foreach ($symbolsAndEndpoints as $symbol => $endpoint) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                echo "Failed to fetch data from endpoint" . "endpoint" . $endpoint;
                continue;
            }
            $price = GetPriceFromBody($response, $name, $symbol);

            //Create Symbol if it doesn't exists
            createSymbol($symbol);

            UpdateLast($price["Last"], $price["Volume"], $price["QuoteVolume"], $price["Symbol"], $price["Exchange"]);
        }
    }
}
