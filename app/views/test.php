<?php
if (!class_exists('Flight')) {
    echo 'Flight non disponible.';
    exit;
}

function testAPI() {
    try {
        $db = Flight::db();

        echo "<h3>1. Test connexion DB</h3>";
        $db->query("SELECT 1");
        echo "✓ Connexion OK<br>";

        echo "<h3>2. Tables disponibles</h3>";
        $tables = $db->query("SHOW TABLES LIKE 'Moto_%'")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            echo "✓ $table<br>";
        }

        echo "<h3>3. Courses en base</h3>";
        $count = $db->query("SELECT COUNT(*) as count FROM Moto_courses")->fetch(PDO::FETCH_ASSOC);
        echo "✓ " . $count['count'] . " courses<br>";

        echo "<h3>4. Test création SQL directe</h3>";
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
            $id = $db->lastInsertId();
            echo "✓ Course créée avec ID: $id<br>";

            $newCourse = $db->query("SELECT * FROM Moto_courses WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
            echo "<pre>" . print_r($newCourse, true) . "</pre>";
        } else {
            echo "✗ Erreur création<br>";
        }
        
    } catch (Exception $e) {
        echo "<div style='color:red;'>ERREUR: " . $e->getMessage() . "</div>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test API</title>
</head>
<body>
    <h1>Test de l'API Courses</h1>
    <?php testAPI(); ?>
    
    <h2>Test via JavaScript</h2>
    <button onclick="testCreate()">Tester création via API</button>
    <div id="result"></div>
    
    <script>
    async function testCreate() {
        const data = {
            date_course: '2025-12-19',
            heure_debut: '14:00',
            heure_fin: '16:00',
            km: 12,
            montant: 18000,
            depart: 'Test Départ',
            arrivee: 'Test Arrivée',
            conducteur_id: 2,
            moto_id: 3,
            client_id: 2
        };
        
        try {
            const res = await fetch('/courses/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            });
            
            const result = await res.json();
            document.getElementById('result').innerHTML = 
                `<pre>${JSON.stringify(result, null, 2)}</pre>`;
        } catch (error) {
            document.getElementById('result').innerHTML = 
                `Erreur: ${error.message}`;
        }
    }
    </script>
    
    <!-- quick access floating buttons -->
    <style>
        #floating-actions {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 9999;
        }
        
        #floating-actions a {
            display: block;
            margin-bottom: 8px;
        }
    </style>
    <div id="floating-actions">
        <a href="/ui/delete-all" class="action-btn danger">Supprimer toutes les courses</a>
        <a href="/" class="action-btn view">Accueil</a>
    </div>
</body>
</html>