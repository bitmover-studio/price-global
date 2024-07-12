<?php
require_once(__DIR__ . '/../db/price.php');

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
                "Last" => floatval($responseObject["lastPrice"]),
                "Volume" => floatval($responseObject["volume"]),
                "QuoteVolume" => floatval($responseObject["quoteVolume"])
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
                "Last" => (float)$responseObject["payload"]["last"],
                "Volume" => (float)$responseObject["payload"]["volume"],
                "QuoteVolume" => (float)$responseObject["payload"]["vwap"]
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
                "Last" => (float)$responseObject["last_trade"],
                "Volume" => (float)$responseObject["rolling_24_hour_volume"],
                "QuoteVolume" => (float)$responseObject["last_trade"] * $responseObject["rolling_24_hour_volume"]
            );

            $price["Last"] = $modifiedResponse["Last"];
            $price["Volume"] = $modifiedResponse["Volume"];
            $price["QuoteVolume"] = $modifiedResponse["QuoteVolume"];
            break;

        default:
            echo "No exchange found on switch case" . "exchange" . $exchange;
    }

    echo "GetPriceFromBody: exchange = " . $exchange . ", symbol = " . $symbol . ", priceToUpdate = " . print_r($price, true);

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

// Rates
function GetRatesFromBody($body, $name, $fiat)
{
    switch ($name) {
        case "Central Bank":
            $responseObject = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Unmarshal error" . ["fiat rates" => $name, "err" => json_last_error_msg()]);
                return ["err" => json_last_error_msg()];
            }
            foreach ($responseObject as $currency => $rate) {
                $newRate = [
                    "Last" => (float)$rate,
                    "Fiat" => substr($currency, 3),
                    "Source" => $name
                ];
                if (!$responseObject) {
                    error_log("Failed to insert fiat rate" . ["fiat" => $newRate["Fiat"], "err" => $newRate["err"]]);
                }
                //Create Symbol if it doesn't exists
                createSymbol($newRate['Fiat']);
                insertFiatRate($newRate["Last"], $newRate['Fiat'], $name);
            }
            echo "GetRatesFromBody: exchange = " . $name . ", rateToInsert = " . print_r($name, true);
            break;

        case "Dolar Blue":
            $responseObject = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Unmarshal error" . ["exchange" => $name, "err" => json_last_error_msg()]);
                return ["err" => json_last_error_msg()];
            }
            $newRate = [
                "Last" => (float)$responseObject["compra"],
                "Fiat" => $fiat,
                "Source" => $name
            ];
            insertFiatRate($newRate["Last"], $newRate['Fiat'], $name);
            echo "GetRatesFromBody: exchange = " . $name . "Last: " . $newRate['Last'] . ", rateToInsert = " . print_r($newRate['Fiat'], true);
            break;

        case "Dolar Paralelo":
            $responseObject = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Unmarshal error" . ["exchange" => $name, "err" => json_last_error_msg()]);
                return ["err" => json_last_error_msg()];
            }
            $newRate = [
                "Last" => (float)$responseObject["promedio"],
                "Fiat" => $fiat,
                "Source" => $name
            ];
            insertFiatRate($newRate["Last"], $newRate['Fiat'], $name);
            echo "GetRatesFromBody: exchange = " . $name . "Last: " . $newRate['Last'] . ", rateToInsert = " . print_r($newRate['Fiat'], true);
            break;

        default:
            error_log("No exchange found on switch case" . ["exchange" => $name]);
    };
}

function fetchRatesFromEndpoints($rates)
{
    foreach ($rates as $rate) {
        $name = $rate["Name"];
        $symbol = $rate["Fiat"];
        $endpoint = $rate["Endpoint"];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            echo "Failed to fetch data from endpoint" . "endpoint" . $endpoint;
            continue;
        }
        GetRatesFromBody($response, $name, $symbol);
    }
}
