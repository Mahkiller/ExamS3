<?php
namespace app\controllers;

use flight\Engine;
use Flight;

class CourseController
{
    // Changez toutes les méthodes statiques en méthodes d'instance
    public function getAll()
    {
        try {
            $app = Flight::app();
            $stmt = $app->db()->query("
                SELECT c.*, 
                       co.nom as conducteur_nom,
                       m.immatriculation as moto_immat,
                       cl.nom as client_nom
                FROM Moto_courses c
                LEFT JOIN Moto_conducteurs co ON c.conducteur_id = co.id
                LEFT JOIN Moto_motos m ON c.moto_id = m.id
                LEFT JOIN Moto_clients cl ON c.client_id = cl.id
                ORDER BY c.date_course DESC, c.heure_debut DESC
            ");
            $courses = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $app->json($courses);
        } catch (\Exception $e) {
            $app->halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public function getOne($id)
    {
        try {
            $app = Flight::app();
            $stmt = $app->db()->prepare("
                SELECT * FROM Moto_courses 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $course = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$course) {
                $app->halt(404, json_encode(['error' => 'Course non trouvée']));
            }
            
            $app->json($course);
        } catch (\Exception $e) {
            $app->halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public function create()
    {
        try {
            $app = Flight::app();
            $data = $app->request()->data;
            
            // Validation des champs requis
            $required = ['date_course', 'km', 'montant', 'conducteur_id', 'moto_id'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $app->halt(400, json_encode(['error' => "Le champ $field est requis"]));
                }
            }
            
            // Préparation des valeurs
            $clientId = !empty($data['client_id']) ? (int)$data['client_id'] : null;
            
            $sql = "INSERT INTO Moto_courses 
                   (date_course, heure_debut, heure_fin, km, montant, depart, arrivee, 
                    conducteur_id, moto_id, client_id, valide)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
            
            $stmt = $app->db()->prepare($sql);
            $success = $stmt->execute([
                $data['date_course'],
                $data['heure_debut'] ?? null,
                $data['heure_fin'] ?? null,
                (float)$data['km'],
                (float)$data['montant'],
                $data['depart'] ?? '',
                $data['arrivee'] ?? '',
                (int)$data['conducteur_id'],
                (int)$data['moto_id'],
                $clientId
            ]);
            
            if ($success) {
                $courseId = $app->db()->lastInsertId();
                $app->json(['success' => true, 'id' => $courseId]);
            } else {
                $app->halt(500, json_encode(['error' => 'Échec de la création']));
            }
            
        } catch (\Exception $e) {
            $app->halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public function update($id)
    {
        try {
            $app = Flight::app();
            
            // Vérifier si la course existe
            $stmt = $app->db()->prepare("SELECT valide FROM Moto_courses WHERE id = ?");
            $stmt->execute([$id]);
            $course = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$course) {
                $app->halt(404, json_encode(['error' => 'Course non trouvée']));
            }
            
            if ($course['valide'] == 1) {
                $app->halt(400, json_encode(['error' => 'Course déjà validée, modification interdite']));
            }
            
            $data = $app->request()->data;
            
            // Préparer les champs à mettre à jour
            $fields = ['date_course', 'heure_debut', 'heure_fin', 'km', 'montant', 
                      'depart', 'arrivee', 'conducteur_id', 'moto_id', 'client_id'];
            $updates = [];
            $values = [];
            
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    if ($field === 'client_id' && empty($data[$field])) {
                        $values[] = null;
                    } else {
                        $values[] = $data[$field];
                    }
                }
            }
            
            if (empty($updates)) {
                $app->halt(400, json_encode(['error' => 'Aucune donnée à mettre à jour']));
            }
            
            $values[] = $id;
            $sql = "UPDATE Moto_courses SET " . implode(', ', $updates) . " WHERE id = ?";
            
            $stmt = $app->db()->prepare($sql);
            $success = $stmt->execute($values);
            
            if ($success) {
                // Enregistrer la modification
                $stmt = $app->db()->prepare("
                    INSERT INTO Moto_modifications_courses 
                    (course_id, date_modification, champ_modifie, valeur_avant, valeur_apres)
                    VALUES (?, CURDATE(), 'Mise à jour complète', 'Ancienne version', 'Nouvelle version')
                ");
                $stmt->execute([$id]);
                
                $app->json(['success' => true]);
            } else {
                $app->halt(500, json_encode(['error' => 'Échec de la mise à jour']));
            }
            
        } catch (\Exception $e) {
            $app->halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public function validate($id)
    {
        try {
            $app = Flight::app();
            $app->db()->beginTransaction();
            
            // Vérifier si la course existe
            $stmt = $app->db()->prepare("SELECT * FROM Moto_courses WHERE id = ? AND valide = 0");
            $stmt->execute([$id]);
            $course = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$course) {
                $app->db()->rollBack();
                $app->halt(404, json_encode(['error' => 'Course non trouvée ou déjà validée']));
            }
            
            // Marquer comme validée
            $stmt = $app->db()->prepare("UPDATE Moto_courses SET valide = 1 WHERE id = ?");
            $stmt->execute([$id]);
            
            // Enregistrer la validation
            $stmt = $app->db()->prepare("
                INSERT INTO Moto_validations (course_id, date_validation)
                VALUES (?, CURDATE())
            ");
            $stmt->execute([$id]);
            
            $app->db()->commit();
            $app->json(['success' => true]);
            
        } catch (\Exception $e) {
            $app->db()->rollBack();
            $app->halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public function delete($id)
    {
        try {
            $app = Flight::app();
            
            // Vérifier si la course existe
            $stmt = $app->db()->prepare("SELECT valide FROM Moto_courses WHERE id = ?");
            $stmt->execute([$id]);
            $course = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$course) {
                $app->halt(404, json_encode(['error' => 'Course non trouvée']));
            }
            
            if ($course['valide'] == 1) {
                $app->halt(400, json_encode(['error' => 'Course validée, suppression interdite']));
            }
            
            // Supprimer d'abord les éventuelles modifications liées
            $stmt = $app->db()->prepare("DELETE FROM Moto_modifications_courses WHERE course_id = ?");
            $stmt->execute([$id]);
            
            // Supprimer la course
            $stmt = $app->db()->prepare("DELETE FROM Moto_courses WHERE id = ?");
            $stmt->execute([$id]);
            
            $app->json(['success' => true]);
            
        } catch (\Exception $e) {
            $app->halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }
}