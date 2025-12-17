<?php

use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
use flight\debug\tracy\TracyExtensionLoader;
use Tracy\Debugger;

/*********************************************
 *         FlightPHP Service Setup           *
 *********************************************
 * This file registers services and integrations
 * for your FlightPHP application. Edit as needed.
 *
 * @var array  $config  From config.php
 * @var Engine $app     FlightPHP app instance
 **********************************************/

// Ensure no accidental output before Tracy
ob_start();

/*********************************************
 *           Session Service Setup           *
 *********************************************
 * To enable sessions in FlightPHP, register the session service.
 * Docs: https://docs.flightphp.com/awesome-plugins/session
 **********************************************/

/*********************************************
 *           Tracy Debugger Setup            *
 *********************************************
 * Tracy is a powerful error handler and debugger for PHP.
 **********************************************/
$ds = DIRECTORY_SEPARATOR; // Define DS for paths
Debugger::enable(); // Auto-detects environment
// Debugger::enable(Debugger::Development); // Explicitly set environment
// Debugger::enable('23.75.345.200'); // Restrict debug bar to specific IPs
Debugger::$logDirectory = __DIR__ . $ds . '..' . $ds . 'log'; // Log directory
Debugger::$strictMode = true; // Show all errors (set to E_ALL & ~E_DEPRECATED for less noise)
// Debugger::$maxLen = 1000; // Max length of dumped variables (default: 150)
// Debugger::$maxDepth = 5; // Max depth of dumped structures (default: 3)
// Debugger::$editor = 'vscode'; // Enable clickable file links in debug bar
// Debugger::$email = 'your@email.com'; // Send error notifications

if (Debugger::$showBar === true && php_sapi_name() !== 'cli') {
    (new TracyExtensionLoader($app)); // Load FlightPHP Tracy extensions
}

/**********************************************
 *           Database Service Setup           *
 **********************************************/
// Uncomment and configure the following for your database:

// MySQL Example:
$dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';

// SQLite Example:
// $dsn = 'sqlite:' . $config['database']['file_path'];

// Register Flight::db() service
$pdoClass = Debugger::$showBar === true ? PdoQueryCapture::class : PdoWrapper::class;
$app->register('db', $pdoClass, [
    $dsn,
    $config['database']['user'] ?? null,
    $config['database']['password'] ?? null
]);

/**********************************************
 *         Third-Party Integrations           *
 **********************************************/
// Google OAuth Example:
// $app->register('google_oauth', Google_Client::class, [ $config['google_oauth'] ]);

// Redis Example:
// $app->register('redis', Redis::class, [ $config['redis']['host'], $config['redis']['port'] ]);

// Add more service registrations below as needed

// Flush buffer after all setup
ob_end_clean();
