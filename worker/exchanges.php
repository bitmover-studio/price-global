<?php

function GetExchanges() {
    $defaultInterval = 43200; // 12 hours
    $lastInterval = 600;      // 10 minutes

    $binanceSymbols = array(
        "BRL" => "https://api.binance.com/api/v3/ticker/24hr?symbol=BTCBRL",
        "TRY" => "https://api.binance.com/api/v3/ticker/24hr?symbol=BTCTRY",
        "USDT" => "https://api.binance.com/api/v3/ticker/24hr?symbol=BTCUSDT",
        "ARS" => "https://api.binance.com/api/v3/ticker/24hr?symbol=BTCARS",
        "JPY" => "https://api.binance.com/api/v3/ticker/24hr?symbol=BTCJPY",
        "UAH" => "https://api.binance.com/api/v3/ticker/24hr?symbol=BTCUAH",
    );

    $krakenSymbols = array(
        "USD" => "https://api.kraken.com/0/public/Ticker?pair=XBTUSD",
        "GBP" => "https://api.kraken.com/0/public/Ticker?pair=XBTGBP",
        "CHF" => "https://api.kraken.com/0/public/Ticker?pair=XBTCHF",
        "EUR" => "https://api.kraken.com/0/public/Ticker?pair=XBTEUR",
        "AUD" => "https://api.kraken.com/0/public/Ticker?pair=XBTAUD",
        "CAD" => "https://api.kraken.com/0/public/Ticker?pair=XBTCAD",
    );

    $BitsoSymbols = array(
        "MXN" => "https://api.bitso.com/v3/ticker/?book=btc_mxn",
    );

    $LunoSymbols = array(
        "NGN" => "https://api.luno.com/api/1/ticker?pair=XBTNGN",
        "ZAR" => "https://api.luno.com/api/1/ticker?pair=XBTZAR",
        // "IDR" => "https://api.luno.com/api/1/ticker?pair=XBTIDR", returned 99999999999 check later
    );

    $exchanges = array(
        array(
            "SymbolsAndEndpoints" => $binanceSymbols,
            "Name" => "Binance",
            "Interval" => $defaultInterval,
            "Last" => $lastInterval,
        ),
        array(
            "SymbolsAndEndpoints" => $krakenSymbols,
            "Name" => "Kraken",
            "Interval" => $defaultInterval,
            "Last" => $lastInterval,
        ),
        array(
            "SymbolsAndEndpoints" => $BitsoSymbols,
            "Name" => "Bitso",
            "Interval" => $defaultInterval,
            "Last" => $lastInterval,
        ),
        array(
            "SymbolsAndEndpoints" => $LunoSymbols,
            "Name" => "Luno",
            "Interval" => $defaultInterval,
            "Last" => $lastInterval,
        ),
    );

    return $exchanges;
}

function GetRates(): array {
    $defaultInterval = 21600; // 21600 sec = 6 hours (Free API key limit)

    $rates = [
        [
            'Name' => 'Central Bank',
            'Endpoint' => 'https://bitcoindata.science/api/fiat_rates.php',
            'Interval' => $defaultInterval,
            'Fiat' => 'All',
        ],
        [
            'Name' => 'Dolar Blue',
            'Endpoint' => 'https://dolarapi.com/v1/dolares/blue',
            'Interval' => $defaultInterval,
            'Fiat' => 'ARS',
        ],
        [
            'Name' => 'Dolar Paralelo',
            'Endpoint' => 'https://ve.dolarapi.com/v1/dolares/paralelo',
            'Interval' => $defaultInterval,
            'Fiat' => 'VES',
        ],
    ];

    return $rates;
}