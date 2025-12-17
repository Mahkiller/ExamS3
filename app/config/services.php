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
 * for your FlightPHP application.
 *
 * @var array  $config  From config.php
 * @var Engine $app     FlightPHP app instance
 **********************************************/

// Ensure no accidental output before Tracy
ob_start();

/*********************************************
 *           Tracy Debugger Setup            *
 *********************************************/
$ds = DIRECTORY_SEPARATOR; // Define DS for paths
Debugger::enable(); // Auto-detects environment
Debugger::$logDirectory = __DIR__ . $ds . '..' . $ds . 'log';
Debugger::$strictMode = true;

if (Debugger::$showBar === true && php_sapi_name() !== 'cli') {
    (new TracyExtensionLoader($app));
}

/**********************************************
 *           Database Service Setup           *
 **********************************************/
// MySQL connection
$dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';
$pdoClass = Debugger::$showBar === true ? PdoQueryCapture::class : PdoWrapper::class;
$app->register('db', $pdoClass, [
    $dsn,
    $config['database']['user'] ?? null,
    $config['database']['password'] ?? null
]);

// Flush buffer after all setup
ob_end_clean();
