<?php

use flight\Engine;
use flight\net\Router;
use app\middlewares\SecurityHeadersMiddleware;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

    // Route principale
    $router->get('/', function() use ($app) {
        // Crée un message à afficher
        $message = '';

        // Récupérer les conducteurs depuis la base
        try {
            $conducteurs = $app->db()->query('SELECT id, nom, salaire_pourcentage FROM Moto_conducteurs')->fetchAll(PDO::FETCH_ASSOC);

            $message .= "Liste des conducteurs :\n";
            foreach ($conducteurs as $c) {
                $message .= "{$c['id']} - {$c['nom']} ({$c['salaire_pourcentage']}%)\n";
            }

        } catch (Exception $e) {
            $message .= "Erreur conducteurs : " . $e->getMessage() . "\n";
        }

        // Récupérer les motos depuis la base
        try {
            $motos = $app->db()->query('SELECT id, immatriculation, consommation_litre_100km, entretien_pourcentage FROM Moto_motos')->fetchAll(PDO::FETCH_ASSOC);

            $message .= "\nListe des motos :\n";
            foreach ($motos as $m) {
                $message .= "{$m['id']} - {$m['immatriculation']} (Conso: {$m['consommation_litre_100km']} L/100km, Entretien: {$m['entretien_pourcentage']}%)\n";
            }

        } catch (Exception $e) {
            $message .= "Erreur motos : " . $e->getMessage() . "\n";
        }

        // Passe le message à la vue
        $app->render('welcome', ['message' => $message]);
    });

    // Route dynamique avec paramètre
    $router->get('/hello-world/@name', function($name) use ($app) {
        $app->render('hello', [ 'name' => $name ]);
    });

}, [ SecurityHeadersMiddleware::class ]);
