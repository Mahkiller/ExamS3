	<?php

	/**********************************************
	 *      FlightPHP Skeleton Sample Config      *
	 **********************************************
	*
	* Copy this file to config.php and update values as needed.
	* All settings are required unless marked as optional.
	*
	* Example:
	*   cp app/config/config_sample.php app/config/config.php
	*
	* This file is NOT tracked by git. Store sensitive credentials here.
	**********************************************/

	/**********************************************
	 *           FlightPHP Core Settings          *
	 **********************************************/
	
	 if (empty($app) === true) {
	 	$app = Flight::app();
	 }
	
	 $app->path(__DIR__ . $ds . '..' . $ds . '..');
	
	 $app->set('flight.base_url', '/');           // Base URL for your app. Change if app is in a subdirectory (e.g., '/myapp/')
	 $app->set('flight.case_sensitive', false);    // Set true for case sensitive routes. Default: false
	 $app->set('flight.log_errors', true);         // Log errors to file. Recommended: true in production
	 $app->set('flight.handle_errors', false);     // Let Tracy handle errors if false. Set true to use Flight's error handler
	 $app->set('flight.views.path', __DIR__ . $ds . '..' . $ds . 'views'); // Path to views/templates
	 $app->set('flight.views.extension', '.php');  // View file extension (e.g., '.php', '.latte')
	 $app->set('flight.content_length', false);    // Send content length header. Usually false unless required by proxy
	
	 $nonce = bin2hex(random_bytes(16));
	 $app->set('csp_nonce', $nonce);
	
	 return [
	 	'database' => [
	 		'host'     => 'localhost',
	 		'dbname'   => '4082_4394',
	 		'user'     => 'root',
	 		'password' => '',
	 	],
	 ];
