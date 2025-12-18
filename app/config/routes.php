<?php

use flight\Engine;
use flight\net\Router;
use app\middlewares\SecurityHeadersMiddleware;

/**
 * @var Router $router
 * @var Engine $app
 */

$router->group('', function(Router $router) use ($app) {

    // Page d'accueil (choix)
    $router->get('/', function() use ($app) {
        $app->render('index');
    });

    // Dashboard (tableau financier)
    $router->get('/dashboard', function() use ($app) {
        $app->render('welcome');
    });

    // UI pages
    $router->get('/ui/courses', function() use ($app) {
        $app->render('courses');
    });

    $router->get('/ui/course/@id', function($id) use ($app) {
        $app->render('modification', ['id' => (int)$id]);
    });

    $router->get('/ui/course-price/@id', function($id) use ($app) {
        // render page for editing course fuel price
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM Moto_courses WHERE id = ?");
        $stmt->execute([(int)$id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        Flight::render('course_price', [
            'id' => (int)$id,
            'course' => $course,
            'prix' => $course['prix_essence'] ?? null
        ]);
    });

    // UI page: modifier prix global essence
    $router->get('/ui/prix-essence', function() use ($app) {
        $db = Flight::db();
        $param = $db->query("SELECT * FROM Moto_parametres WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
        Flight::render('essence', ['prix_global' => $param['prix_essence'] ?? null]);
    });


    // replace or ensure this handler exists (GET /courses)
    $router->get('/courses', function() {
        try {
            $db = Flight::db();
            $sql = "
                SELECT
                    c.*,
                    DATE(c.date_course) AS date_course,
                    co.nom AS conducteur_nom, co.salaire_pourcentage,
                    m.immatriculation AS moto_immat, m.entretien_pourcentage,
                    cl.nom AS client_nom
                FROM Moto_courses c
                LEFT JOIN Moto_conducteurs co ON c.conducteur_id = co.id
                LEFT JOIN Moto_motos m ON c.moto_id = m.id
                LEFT JOIN Moto_clients cl ON c.client_id = cl.id
                ORDER BY c.date_course DESC, c.id DESC
            ";
            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // calculs financiers par course
            foreach ($rows as &$r) {
                $montant = (float)($r['montant'] ?? 0);
                $salaire_pct = (float)($r['salaire_pourcentage'] ?? 0);
                $entretien_pct = (float)($r['entretien_pourcentage'] ?? 0);
                $r['salaire'] = $montant * ($salaire_pct / 100.0);
                $r['entretien'] = $montant * ($entretien_pct / 100.0);
                $r['depense'] = $r['salaire'] + $r['entretien'];
                $r['benefice'] = $montant - $r['depense'];
            }
            Flight::json(['success' => true, 'data' => $rows], 200);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    $router->get('/courses/@id', function($id) {
        $db = Flight::db();
        $stmt = $db->prepare("
            SELECT c.*, co.nom AS conducteur_nom, m.immatriculation AS moto_immat, cl.nom AS client_nom
            FROM Moto_courses c
            LEFT JOIN Moto_conducteurs co ON c.conducteur_id = co.id
            LEFT JOIN Moto_motos m ON c.moto_id = m.id
            LEFT JOIN Moto_clients cl ON c.client_id = cl.id
            WHERE c.id = ?
            LIMIT 1
        ");
        $stmt->execute([(int)$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            Flight::json(['success' => false, 'message' => 'Course introuvable'], 404);
            return;
        }
        Flight::json(['success' => true, 'data' => $row]);
    });

    $router->post('/courses', function() {
        $data = Flight::request()->data;
        $required = ['conducteur_id','moto_id','date_course','km','montant'];
        foreach ($required as $k) {
            if (!isset($data[$k]) || trim((string)$data[$k]) === '') {
                Flight::json(['success'=>false,'message'=>"Champ manquant: $k"], 400);
                return;
            }
        }
        $db = Flight::db();
        try {
            $sql = "INSERT INTO Moto_courses (conducteur_id, moto_id, client_id, date_course, heure_debut, heure_fin, km, montant, depart, arrivee, valide)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['conducteur_id'],
                $data['moto_id'],
                $data['client_id'] ?? null,
                $data['date_course'],
                $data['heure_debut'] ?? null,
                $data['heure_fin'] ?? null,
                $data['km'],
                $data['montant'],
                $data['depart'] ?? null,
                $data['arrivee'] ?? null
            ]);

            // try to obtain last insert id in a robust way
            $insertId = null;
            if (is_callable([$db, 'lastInsertId'])) {
                $insertId = $db->lastInsertId();
            } elseif (isset($db->pdo) && is_callable([$db->pdo, 'lastInsertId'])) {
                $insertId = $db->pdo->lastInsertId();
            } else {
                try {
                    $r = $db->query("SELECT LAST_INSERT_ID() AS id")->fetch(PDO::FETCH_ASSOC);
                    $insertId = $r['id'] ?? null;
                } catch (Exception $_) {
                    $insertId = null;
                }
            }

            Flight::json(['success'=>true,'message'=>'Course créée','id'=>$insertId], 201);
        } catch (Exception $e) {
            Flight::json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    });

    $router->post('/courses/update/@id', function($id) {
        $data = Flight::request()->data;
        $db = Flight::db();
        try {
            $sql = "UPDATE Moto_courses SET conducteur_id=?, moto_id=?, client_id=?, date_course=?, heure_debut=?, heure_fin=?, km=?, montant=?, depart=?, arrivee=? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['conducteur_id'] ?? null,
                $data['moto_id'] ?? null,
                $data['client_id'] ?? null,
                $data['date_course'] ?? null,
                $data['heure_debut'] ?? null,
                $data['heure_fin'] ?? null,
                $data['km'] ?? null,
                $data['montant'] ?? null,
                $data['depart'] ?? null,
                $data['arrivee'] ?? null,
                (int)$id
            ]);
            Flight::json(['success'=>true,'message'=>'Course mise à jour']);
        } catch (Exception $e) {
            Flight::json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    });

    $router->post('/courses/validate/@id', function($id) {
        $db = Flight::db();
        try {
            $stmt = $db->prepare("UPDATE Moto_courses SET valide = 1 WHERE id = ?");
            $stmt->execute([(int)$id]);
            Flight::json(['success'=>true,'message'=>'Course validée']);
        } catch (Exception $e) {
            Flight::json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    });

    $router->delete('/courses/delete/@id', function($id) {
        $db = Flight::db();
        try {
            $stmt = $db->prepare("DELETE FROM Moto_courses WHERE id = ?");
            $stmt->execute([(int)$id]);
            Flight::json(['success'=>true,'message'=>'Course supprimée']);
        } catch (Exception $e) {
            Flight::json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    });

    // compatibility: also accept POST for delete (some clients/servers)
    $router->post('/courses/delete/@id', function($id) {
        $db = Flight::db();
        try {
            $stmt = $db->prepare("DELETE FROM Moto_courses WHERE id = ?");
            $stmt->execute([(int)$id]);
            Flight::json(['success'=>true,'message'=>'Course supprimée']);
        } catch (Exception $e) {
            Flight::json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    });

    $router->post('/courses/price/@id', function($id) {
        $data = Flight::request()->data;
        if (!isset($data['prix'])) {
            Flight::json(['success'=>false,'message'=>'paramètre prix manquant'], 400);
            return;
        }
        $prix = (string)$data['prix'];
        if ($prix === '' || !is_numeric($prix)) {
            Flight::json(['success'=>false,'message'=>'prix invalide'], 400);
            return;
        }
        $db = Flight::db();
        try {
            $stmt = $db->prepare("UPDATE Moto_courses SET prix_essence = ? WHERE id = ?");
            $stmt->execute([(float)$prix, (int)$id]);
            Flight::json(['success'=>true,'message'=>'Prix enregistré','prix'=> (float)$prix]);
        } catch (Exception $e) {
            Flight::json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    });

    $router->delete('/courses/price/@id', function($id) {
        $db = Flight::db();
        try {
            $stmt = $db->prepare("UPDATE Moto_courses SET prix_essence = NULL WHERE id = ?");
            $stmt->execute([(int)$id]);
            Flight::json(['success'=>true,'message'=>'Prix local supprimé']);
        } catch (Exception $e) {
            Flight::json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    });

    // parametres prix global
    $router->post('/parametres/prix-essence', function() {
        $data = Flight::request()->data;
        $prix = trim((string)($data['prix'] ?? ''));
        if ($prix === '' || !is_numeric($prix)) {
            Flight::json(['success'=>false,'message'=>'prix invalide'], 400);
            return;
        }
        $db = Flight::db();
        try {
            $stmt = $db->prepare("INSERT INTO Moto_parametres (id, prix_essence) VALUES (1, ?) ON DUPLICATE KEY UPDATE prix_essence = VALUES(prix_essence)");
            $stmt->execute([(float)$prix]);

            $hstmt = $db->prepare("INSERT INTO Moto_prix_essence_historique (prix_essence) VALUES (?)");
            $hstmt->execute([(float)$prix]);

            Flight::json(['success'=>true,'message'=>'Prix global mis à jour','prix'=> (float)$prix]);
        } catch (Exception $e) {
            Flight::json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    });

    // Page UI pour supprimer toutes les courses (code confirmation requis)
    $router->get('/ui/delete-all', function() use ($app) {
        $app->render('delete_all');
    });

    // API : suppression de toutes les courses (POST) — code requis "1234"
    $router->post('/courses/delete-all', function() {
        $data = Flight::request()->data;
        $code = trim((string)($data['code'] ?? ''));

        if ($code !== '1234') {
            Flight::json(['success' => false, 'message' => 'Code de confirmation invalide'], 403);
            return;
        }

        $db = Flight::db();
        try {
            // supprimer toutes les entrées
            $stmt = $db->prepare("DELETE FROM Moto_courses");
            $stmt->execute();
            $deleted = $stmt->rowCount();

            // optionnel : reset auto_increment si nécessaire
            try {
                $db->query("ALTER TABLE Moto_courses AUTO_INCREMENT = 1");
            } catch (Exception $_) {
                // ignore si non supporté
            }

            Flight::json(['success' => true, 'deleted' => $deleted, 'message' => 'Toutes les courses ont été supprimées.']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

}, [ SecurityHeadersMiddleware::class ]);