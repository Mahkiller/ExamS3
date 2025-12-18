<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/config/bootstrap.php';

echo "<h1>Debug Mode</h1>";

// Test 1: Connexion DB
try {
    $db = Flight::db();
    echo "✓ DB Connection: OK<br>";
    
    // Test 2: Vérifier les tables
    $tables = $db->query("SHOW TABLES LIKE 'Moto_%'")->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Tables found: " . implode(', ', $tables) . "<br>";
    
    // Test 3: Créer une course directement
    $sql = "INSERT INTO Moto_courses 
            (date_course, heure_debut, heure_fin, km, montant, depart, arrivee, 
             conducteur_id, moto_id, client_id, valide)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
    
    $stmt = $db->prepare($sql);
    $result = $stmt->execute([
        '2025-12-18',
        '10:00',
        '15:00',
        10,
        15000,
        'Ankatso',
        'Andoranofotsy',
        1,
        2,
        1
    ]);
    
    if ($result) {
        echo "✓ Direct SQL Insert: OK (ID: " . $db->lastInsertId() . ")<br>";
    } else {
        echo "✗ Direct SQL Insert: FAILED<br>";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}