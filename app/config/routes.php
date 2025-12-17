<?php

use flight\Engine;
use flight\net\Router;
use app\middlewares\SecurityHeadersMiddleware;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// Wrap all routes with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

    // Route principale avec affichage des conducteurs
    $router->get('/', function() use ($app) {
        $message = '';

        try {
            // Récupérer tous les conducteurs depuis la base
            $conducteurs = $app->db()->query('SELECT id, nom, salaire_pourcentage FROM Moto_conducteurs')->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($conducteurs)) {
                $message = "Liste des conducteurs :\n";
                foreach ($conducteurs as $c) {
                    $message .= "{$c['id']} - {$c['nom']} ({$c['salaire_pourcentage']}%)\n";
                }
            } else {
                $message = "Aucun conducteur trouvé dans la base.";
            }

        } catch (Exception $e) {
            $message = "Erreur lors de la récupération des conducteurs : " . $e->getMessage();
        }

        $app->render('welcome', ['message' => $message]);
    });

    // Route dynamique avec paramètre exemple
    $router->get('/hello-world/@name', function($name) use ($app) {
        $app->render('hello', [ 'name' => $name ]);
    });

}, [ SecurityHeadersMiddleware::class ]);
