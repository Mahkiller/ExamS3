<?php

use flight\database\PdoWrapper;

/*
 * FlightPHP Framework
 * @copyright   Copyright (c) 2024, Mike Cao <mike@mikecao.com>
 * @license     MIT, http://flightphp.com/license
                                                                  .____   __ _
     __o__   _______ _ _  _                                     /     /
     \    ~\                                                  /      /
       \     '\                                         ..../      .'
        . ' ' . ~\                                      ' /       /
       .  _    .  ~ \  .+~\~ ~ ' ' " " ' ' ~ - - - - - -''_      /
      .  <#  .  - - -/' . ' \  __                          '~ - \
       .. -           ~-.._ / |__|  ( )  ( )  ( )  0  o    _ _    ~ .
     .-'                                               .- ~    '-.    -.
    <                      . ~ ' ' .             . - ~             ~ -.__~_. _ _
      ~- .       N121PP  .          . . . . ,- ~
            ' ~ - - - - =.   <#>    .         \.._
                        .     ~      ____ _ .. ..  .- .
                         .         '        ~ -.        ~ -.
                           ' . . '               ~ - .       ~-.
                                                       ~ - .      ~ .
                                                              ~ -...0..~. ____
   Cessna 402  (Wings)
   by Dick Williams, rjw1@tyrell.net
*/

$ds = DIRECTORY_SEPARATOR;

require(__DIR__ . $ds . '..' . $ds . 'vendor' . $ds . 'autoload.php');

$config_file_path = __DIR__. $ds . '..' . $ds . 'app/config/config.php';
if(file_exists($config_file_path) === false) {
    Flight::halt(500, 'Config file not found. Please create a config.php file in the app/config directory to get started.');
}
$config = require($config_file_path);

$dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';
Flight::register('db', PdoWrapper::class, [ $dsn, $config['database']['user'], $config['database']['password'] ]);

Flight::route('GET /', function() {
    $message = 'Bienvenue dans l’application Coopérative Moto!';
    Flight::render('welcome', ['message' => $message]);
});

Flight::route('GET /hello-world/@name', function($name) {
    $message = "Hello world! Oh hey $name!";
    Flight::render('welcome', ['message' => $message]);
});

Flight::group('/courses', function() {

    Flight::route('GET /', function() {
        $db = Flight::db();
        $courses = $db->query("SELECT c.id, c.date_course, c.km, c.montant, c.depart, c.arrivee, c.valide,
                                       m.nom AS conducteur, t.immatriculation AS moto
                                FROM Moto_courses c
                                JOIN Moto_conducteurs m ON c.conducteur_id = m.id
                                JOIN Moto_motos t ON c.moto_id = t.id
                                ORDER BY c.date_course DESC")->fetchAll();
        Flight::json($courses, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    });

    Flight::route('POST /create', function() {
        $data = Flight::request()->data;
        $db = Flight::db();
        $db->run(
            "INSERT INTO Moto_courses (conducteur_id, moto_id, date_course, heure_debut, heure_fin, km, montant, depart, arrivee) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['conducteur_id'],
                $data['moto_id'],
                $data['date_course'],
                $data['heure_debut'] ?? null,
                $data['heure_fin'] ?? null,
                $data['km'],
                $data['montant'],
                $data['depart'] ?? null,
                $data['arrivee'] ?? null
            ]
        );
        Flight::json(['success' => true], 200);
    });

    Flight::route('POST /validate/@id:[0-9]+', function($id) {
        $db = Flight::db();
        $db->run("UPDATE Moto_courses SET valide = 1 WHERE id = ?", [ $id ]);
        Flight::json(['success' => true], 200);
    });

    Flight::route('DELETE /delete/@id:[0-9]+', function($id) {
        $db = Flight::db();
        $db->run("DELETE FROM Moto_courses WHERE id = ?", [ $id ]);
        Flight::json(['success' => true], 200);
    });

});

Flight::start();
