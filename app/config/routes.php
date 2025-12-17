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

		try {
			$courses = $app->db()->query('SELECT id, conducteur_id, moto_id, date_course, heure_debut, heure_fin, km, montant, depart, arrivee, valide FROM Moto_courses')->fetchAll(PDO::FETCH_ASSOC);

			$message .= "\nListe des courses :\n";
			foreach ($courses as $c) {
				$status = $c['valide'] ? "✓ Validée" : "⨯ Non validée";
				$message .= "ID: {$c['id']} - Date: {$c['date_course']} ({$c['heure_debut']} - {$c['heure_fin']})\n";
				$message .= "  Conducteur: {$c['conducteur_id']}, Moto: {$c['moto_id']}, Distance: {$c['km']}km\n";
				$message .= "  Montant: {$c['montant']}€, Trajet: {$c['depart']} → {$c['arrivee']}, Statut: {$status}\n";
			}
			
		} catch (Exception $e) {
			$message .= "Erreur courses : " . $e->getMessage() . "\n";
		}

		// 1. Paramètres (prix essence en Ariary)
		try {
			$parametres = $app->db()->query('SELECT * FROM Moto_parametres ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

			$message .= "\n=== PARAMÈTRES DU SYSTÈME ===\n";
			if (empty($parametres)) {
				$message .= "⚠️ Aucun prix d'essence configuré\n";
			} else {
				// Afficher le dernier prix en premier
				$dernier = $parametres[0];
				$message .= "💰 Prix essence actuel : " . number_format($dernier['prix_essence'], 0, '', ' ') . " MGA/L\n";
				
				if (count($parametres) > 1) {
					$message .= "\n📊 Historique des prix :\n";
					foreach ($parametres as $p) {
						$message .= "  • ID {$p['id']} : " . number_format($p['prix_essence'], 0, '', ' ') . " MGA/L\n";
					}
				}
			}

		} catch (Exception $e) {
			$message .= "❌ Erreur paramètres : " . $e->getMessage() . "\n";
		}

		// 2. Validations des courses
		try {
			$validations = $app->db()->query('SELECT v.*, c.date_course, c.montant 
											FROM Moto_validations v 
											LEFT JOIN Moto_courses c ON v.course_id = c.id 
											ORDER BY v.date_validation DESC')->fetchAll(PDO::FETCH_ASSOC);

			$message .= "\n=== VALIDATIONS DES COURSES ===\n";
			if (empty($validations)) {
				$message .= "📭 Aucune validation enregistrée\n";
			} else {
				$message .= "📋 Total : " . count($validations) . " validation(s)\n\n";
				
				$total_montant = 0;
				foreach ($validations as $v) {
					$montant_formatted = number_format($v['montant'], 0, '', ' ');
					$total_montant += $v['montant'];
					
					$message .= "✅ Validation #{$v['id']}\n";
					$message .= "   Course : #{$v['course_id']} (du {$v['date_course']})\n";
					$message .= "   Montant : {$montant_formatted} MGA\n";
					$message .= "   Date validation : {$v['date_validation']}\n";
				}
				
				$message .= "\n💵 Total validé : " . number_format($total_montant, 0, '', ' ') . " MGA\n";
			}

		} catch (Exception $e) {
			$message .= "❌ Erreur validations : " . $e->getMessage() . "\n";
		}

        // Passe le message à la vue
        $app->render('welcome', ['message' => $message]);
    });

    // Route dynamique avec paramètre
    $router->get('/hello-world/@name', function($name) use ($app) {
        $app->render('hello', [ 'name' => $name ]);
    });

}, [ SecurityHeadersMiddleware::class ]);
