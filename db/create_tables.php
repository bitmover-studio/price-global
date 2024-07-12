<?php
require_once "db_connection.php";

$create_exchanges_table = "CREATE TABLE IF NOT EXISTS exchanges (
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    name VARCHAR(36) PRIMARY KEY,
    url TEXT
)";

$create_symbols_table = "CREATE TABLE IF NOT EXISTS symbols (
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    name TEXT,
    symbol VARCHAR(36) PRIMARY KEY
)";

$create_pricelast_table = "CREATE TABLE IF NOT EXISTS price_last (
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last DECIMAL(10,2),
    volume DECIMAL(10,8) NOT NULL,
    quote_volume DECIMAL(10,2) NOT NULL,
    symbol VARCHAR(36),
    exchange_name VARCHAR(36),
    PRIMARY KEY (symbol),
    FOREIGN KEY (exchange_name) REFERENCES exchanges(name),
    FOREIGN KEY (symbol) REFERENCES symbols(symbol)
)";

$create_fiat_rates_table = "CREATE TABLE IF NOT EXISTS fiat_rates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last DECIMAL(10,2),
    fiat VARCHAR(36) NOT NULL,
    source text NOT NULL,
    FOREIGN KEY (fiat) REFERENCES symbols(symbol)
)";

$create_index = "ALTER TABLE price_last ADD INDEX price_last_symbol_created_at_last_idx (symbol, created_at, last);";
